<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Suscripcion;
use App\Models\Ensenanza;
use App\Models\Traduccion;
use App\Models\HistorialEnvio;
use App\Models\CatEstadoEnvio;
use Carbon\Carbon;

class SendDailyTeachings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'teachings:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía enseñanzas programadas a los usuarios';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Buscar suscripciones activas que deben recibir envío hoy
        $suscripciones = Suscripcion::where('estado_id', 1)
            ->where('fecha_proximo_envio', '<=', Carbon::now())
            ->get();

        foreach ($suscripciones as $suscripcion) {
            $this->enviarEnsenanza($suscripcion);
    }

        $this->info('Enseñanzas enviadas: ' . $suscripciones->count());
    }

    private function enviarEnsenanza($suscripcion)
    {
        // Obtener siguiente enseñanza
        $ensenanza = Ensenanza::where('libro_id', $suscripcion->libro_id)
            ->where('orden', '>', $suscripcion->ultima_ensenanza_enviada)
            ->orderBy('orden')
            ->first();

        if (!$ensenanza) {
            // Libro completado
            $suscripcion->estado_id = 2; // Completado
            $suscripcion->save();
            return;
        }

        // Obtener idiomas solicitados por el usuario para este libro
        $idiomas = $suscripcion->idiomas()->get();

        foreach ($idiomas as $idioma) {
            // Buscar traducción o usar texto original
            $texto = $ensenanza->texto_original;
            
            if ($idioma->id != $suscripcion->libro->idioma_original_id) {
                $traduccion = Traduccion::where('ensenanza_id', $ensenanza->id)
                    ->where('idioma_id', $idioma->id)
                    ->first();
                
                if ($traduccion) {
                    $texto = $traduccion->texto_traducido;
                }
            }

            // Aquí iría el envío del email
            // Mail::to($suscripcion->usuario->email)->send(...);

            // Registrar en historial
            HistorialEnvio::create([
                'usuario_id' => $suscripcion->usuario_id,
                'ensenanza_id' => $ensenanza->id,
                'idioma_id' => $idioma->id,
                'estado_id' => 2, // Entregado
                'fecha_envio' => Carbon::now(),
            ]);
        }

        // Actualizar progreso
        $suscripcion->ultima_ensenanza_enviada = $ensenanza->orden;
        $suscripcion->fecha_proximo_envio = $this->calcularProximoEnvio($suscripcion);
        $suscripcion->save();
    }

    private function calcularProximoEnvio($suscripcion)
    {
        $frecuencia = $suscripcion->usuario->frecuencia;
        return Carbon::now()->addDays($frecuencia->dias_entre_envios);
    }
}