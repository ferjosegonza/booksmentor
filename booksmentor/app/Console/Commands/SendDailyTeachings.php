<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\Suscripcion;
use App\Models\Ensenanza;
use App\Models\Traduccion;
use App\Models\HistorialEnvio;
use App\Models\CatEstadoEnvio;
use App\Mail\EnsenanzaMail;
use App\Services\LLM\LLMService;
use Carbon\Carbon;

class SendDailyTeachings extends Command
{
    protected $signature = 'teachings:send {--suscripcion= : ID específico de suscripción a procesar} {--force : Forzar envío ignorando fecha próxima}';
    protected $description = 'Envía enseñanzas programadas a los usuarios por email';

    public function handle(LLMService $llmService)
    {
        $this->info('Iniciando envío de enseñanzas programadas...');

        $query = Suscripcion::with(['usuario.frecuencia', 'libro.idiomaOriginal', 'idiomas'])
            ->where('estado_id', 1) // Activo
            ->whereHas('usuario', function ($q) {
                $q->where('activo', true);
            });

        $suscripcionId = $this->option('suscripcion');
        if ($suscripcionId) {
            $query->where('id', $suscripcionId);
        } elseif (!$this->option('force')) {
            $query->where(function ($q) {
                $q->whereNull('fecha_proximo_envio')
                  ->orWhere('fecha_proximo_envio', '<=', Carbon::now());
            });
        }

        $suscripciones = $query->get();
        $total = $suscripciones->count();
        $this->info("Suscripciones elegibles encontradas: {$total}");

        $enviados = 0;
        $errores = 0;

        foreach ($suscripciones as $suscripcion) {
            $resultado = $this->enviarEnsenanza($suscripcion, $llmService);
            if ($resultado) {
                $enviados++;
            } else {
                $errores++;
            }
        }

        $this->info("Proceso finalizado. Total procesados: {$total}, Enviados con éxito: {$enviados}, Errores/Completados: {$errores}");
        return 0;
    }

    public function enviarEnsenanza(Suscripcion $suscripcion, LLMService $llmService): bool
    {
        $libro = $suscripcion->libro;
        $usuario = $suscripcion->usuario;

        if (!$libro || !$usuario || empty($usuario->email)) {
            return false;
        }

        // Obtener siguiente enseñanza
        $proximoOrden = $suscripcion->ultima_ensenanza_enviada + 1;
        $ensenanza = Ensenanza::where('libro_id', $libro->id)
            ->where('orden', $proximoOrden)
            ->first();

        // Si no hay con orden exacto, buscar la menor mayor a la última
        if (!$ensenanza) {
            $ensenanza = Ensenanza::where('libro_id', $libro->id)
                ->where('orden', '>', $suscripcion->ultima_ensenanza_enviada)
                ->orderBy('orden')
                ->first();
        }

        if (!$ensenanza) {
            // Libro completado por el usuario
            $suscripcion->estado_id = 2; // Completado
            $suscripcion->fecha_proximo_envio = null;
            $suscripcion->save();
            $this->line("El usuario {$usuario->email} ha completado el libro: {$libro->titulo}");
            return false;
        }

        // Obtener idiomas solicitados por el usuario para esta suscripción
        $idiomas = $suscripcion->idiomas()->get();
        if ($idiomas->isEmpty()) {
            $idiomas = collect([$libro->idiomaOriginal]);
        }

        $textosPorIdioma = [];

        foreach ($idiomas as $idioma) {
            $texto = $ensenanza->texto_original;

            if ($idioma->id != $libro->idioma_original_id) {
                $traduccion = Traduccion::where('ensenanza_id', $ensenanza->id)
                    ->where('idioma_id', $idioma->id)
                    ->first();

                if ($traduccion) {
                    $texto = $traduccion->texto_traducido;
                    $traduccion->increment('veces_usado');
                    $traduccion->update(['ultimo_uso' => Carbon::now()]);
                } else {
                    // Generar traducción al vuelo con LLM y guardarla en caché
                    $texto = $llmService->translateText(
                        $ensenanza->texto_original,
                        $libro->idiomaOriginal ? $libro->idiomaOriginal->codigo : 'es',
                        $idioma->codigo
                    );

                    Traduccion::create([
                        'ensenanza_id' => $ensenanza->id,
                        'idioma_id' => $idioma->id,
                        'texto_traducido' => $texto,
                        'fecha_traduccion' => Carbon::now(),
                        'veces_usado' => 1,
                        'ultimo_uso' => Carbon::now(),
                    ]);
                }
            }

            $textosPorIdioma[] = [
                'idioma' => $idioma,
                'texto' => $texto
            ];
        }

        // Enviar email real
        $estadoEnvioId = 2; // 2 = Entregado
        try {
            Mail::to($usuario->email)->send(new EnsenanzaMail($usuario, $libro, $ensenanza, $textosPorIdioma));
            $this->info("Email enviado a {$usuario->email} para {$libro->titulo} (Lección #{$ensenanza->orden})");
        } catch (\Exception $e) {
            $estadoEnvioId = 5; // 5 = Fallido
            Log::error("Error enviando email a {$usuario->email}: " . $e->getMessage());
            $this->error("Fallo al enviar email a {$usuario->email}: " . $e->getMessage());
        }

        // Registrar en historial para cada idioma
        foreach ($textosPorIdioma as $tItem) {
            HistorialEnvio::create([
                'usuario_id' => $usuario->id,
                'ensenanza_id' => $ensenanza->id,
                'idioma_id' => $tItem['idioma']->id,
                'estado_id' => $estadoEnvioId,
                'fecha_envio' => Carbon::now(),
            ]);
        }

        // Actualizar progreso de suscripción
        $suscripcion->ultima_ensenanza_enviada = $ensenanza->orden;
        $suscripcion->fecha_proximo_envio = $this->calcularProximoEnvio($suscripcion);

        // Si ya llegó a la última enseñanza
        if ($ensenanza->orden >= $libro->cantidad_ensenanzas) {
            $suscripcion->estado_id = 2; // Completado
        }

        $suscripcion->save();
        return $estadoEnvioId === 2;
    }

    private function calcularProximoEnvio(Suscripcion $suscripcion): Carbon
    {
        $frecuencia = $suscripcion->usuario->frecuencia;
        $dias = $frecuencia ? $frecuencia->dias_entre_envios : 1;

        $next = Carbon::now()->addDays($dias);

        // Si es "Solo laborables" (id 2) y cae en sábado o domingo
        if ($frecuencia && $frecuencia->id == 2) {
            if ($next->isSaturday()) {
                $next->addDays(2);
            } elseif ($next->isSunday()) {
                $next->addDays(1);
            }
        }

        // Ajustar hora si el usuario tiene hora preferida
        if (!empty($suscripcion->usuario->hora_envio)) {
            $partes = explode(':', $suscripcion->usuario->hora_envio);
            $next->setTime((int)$partes[0], (int)($partes[1] ?? 0), 0);
        }

        return $next;
    }
}