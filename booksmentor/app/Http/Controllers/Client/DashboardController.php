<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Libro;
use App\Models\Ensenanza;
use App\Models\Traduccion;
use App\Models\Suscripcion;
use App\Models\Usuario;
use App\Models\CatIdioma;
use App\Models\CatTag;
use App\Models\CatFrecuencia;
use App\Models\CatPlane;
use App\Models\CatTipoSugerencia;
use App\Models\SugerenciaUsuario;
use App\Models\HistorialEnvio;
use App\Services\LLM\LLMService;
use App\Console\Commands\SendDailyTeachings;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private function getUsuario(): Usuario
    {
        $user = Auth::user();
        $usuario = Usuario::where('email', $user->email)->orWhere('user_id', $user->id)->first();
        if (!$usuario) {
            $usuario = Usuario::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'nombre' => $user->name,
                'frecuencia_id' => 1,
                'plan_id' => 1,
                'hora_envio' => '08:00:00',
                'zona_horaria' => 'America/Argentina/Buenos_Aires',
                'activo' => true,
            ]);
        }
        return $usuario;
    }

    public function index()
    {
        $usuario = $this->getUsuario();
        
        $suscripcionesActivas = Suscripcion::where('usuario_id', $usuario->id)
            ->where('estado_id', 1)
            ->with(['libro.idiomaOriginal', 'libro.tags', 'idiomas'])
            ->get();

        $suscripcionesCompletadas = Suscripcion::where('usuario_id', $usuario->id)
            ->where('estado_id', 2)
            ->with(['libro'])
            ->get();

        $totalLibros = $suscripcionesActivas->count() + $suscripcionesCompletadas->count();
        $proximoEnvio = $suscripcionesActivas->sortBy('fecha_proximo_envio')->first();

        $historialReciente = HistorialEnvio::where('usuario_id', $usuario->id)
            ->with(['ensenanza.libro', 'idioma', 'estado'])
            ->orderBy('fecha_envio', 'desc')
            ->take(5)
            ->get();

        $idiomas = CatIdioma::where('activo', true)->get();
        $catalogoDestacado = Libro::where('activo', true)
            ->whereNotIn('id', Suscripcion::where('usuario_id', $usuario->id)->pluck('libro_id'))
            ->take(4)
            ->get();

        return view('dashboard.index', compact(
            'usuario',
            'suscripcionesActivas',
            'suscripcionesCompletadas',
            'totalLibros',
            'proximoEnvio',
            'historialReciente',
            'idiomas',
            'catalogoDestacado'
        ));
    }

    public function explorar(Request $request)
    {
        $usuario = $this->getUsuario();
        $query = Libro::where('activo', true)->with(['idiomaOriginal', 'tags']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('titulo', 'LIKE', "%{$buscar}%")
                  ->orWhere('autor', 'LIKE', "%{$buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$buscar}%");
            });
        }

        if ($request->filled('tag')) {
            $tagSlug = $request->tag;
            $query->whereHas('tags', function($q) use ($tagSlug) {
                $q->where('slug', $tagSlug);
            });
        }

        $libros = $query->paginate(12)->withQueryString();
        $tags = CatTag::all();
        $idiomas = CatIdioma::where('activo', true)->get();
        $misLibroIds = Suscripcion::where('usuario_id', $usuario->id)->pluck('libro_id')->toArray();

        return view('dashboard.explorar', compact('libros', 'tags', 'idiomas', 'misLibroIds'));
    }

    public function suscribir(Request $request)
    {
        $request->validate([
            'libro_id' => 'required|exists:libros,id',
            'idiomas' => 'required|array|min:1',
            'idiomas.*' => 'exists:cat_idiomas,id',
        ]);

        $usuario = $this->getUsuario();

        // Check plan limits
        $maxLibros = $usuario->plan ? $usuario->plan->max_libros : 1;
        $actuales = Suscripcion::where('usuario_id', $usuario->id)->where('estado_id', 1)->count();

        if ($actuales >= $maxLibros) {
            return redirect()->route('dashboard.perfil')->with('error', "Has alcanzado el límite de libros activos para tu plan ({$maxLibros}). Mejora tu plan para suscribirte a más libros.");
        }

        $maxIdiomas = $usuario->plan ? $usuario->plan->max_idiomas : 1;
        $idiomasSeleccionados = array_slice($request->idiomas, 0, $maxIdiomas);

        $sub = Suscripcion::firstOrCreate(
            ['usuario_id' => $usuario->id, 'libro_id' => $request->libro_id],
            [
                'estado_id' => 1,
                'ultima_ensenanza_enviada' => 0,
                'fecha_proximo_envio' => Carbon::now()->addMinutes(5),
            ]
        );

        $sub->idiomas()->sync($idiomasSeleccionados);

        return redirect()->route('dashboard.suscripciones')->with('success', '¡Te has suscrito con éxito a este libro!');
    }

    public function crearLibro()
    {
        $idiomas = CatIdioma::where('activo', true)->get();
        $tags = CatTag::all();

        return view('dashboard.libros.crear', compact('idiomas', 'tags'));
    }

    public function guardarLibro(Request $request, LLMService $llmService)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'portada_url' => 'nullable|url',
            'idioma_original_id' => 'required|exists:cat_idiomas,id',
            'target_idiomas' => 'required|array|min:1',
            'target_idiomas.*' => 'exists:cat_idiomas,id',
            'contenido' => 'required|string|min:20',
            'cantidad_ensenanzas' => 'nullable|integer|min:1|max:30',
            'tags' => 'nullable|array',
        ]);

        $usuario = $this->getUsuario();
        $count = $request->cantidad_ensenanzas ?: 10;

        try {
            $libro = $llmService->processBookUpload(
                $request->titulo,
                $request->autor,
                $request->descripcion ?: '',
                $request->portada_url,
                (int) $request->idioma_original_id,
                $request->target_idiomas,
                $request->contenido,
                $usuario->id,
                $count,
                $request->tags ?: []
            );

            return redirect()->route('dashboard.suscripciones')->with('success', "¡Libro '{$libro->titulo}' procesado y traducido exitosamente con {$libro->cantidad_ensenanzas} lecciones!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error procesando el libro con el LLM: ' . $e->getMessage());
        }
    }

    public function bulkLibros()
    {
        $idiomas = CatIdioma::where('activo', true)->get();
        return view('dashboard.libros.bulk', compact('idiomas'));
    }

    public function guardarBulkLibros(Request $request, LLMService $llmService)
    {
        $request->validate([
            'bulk_data' => 'required|string',
            'target_idiomas' => 'required|array|min:1',
            'target_idiomas.*' => 'exists:cat_idiomas,id',
        ]);

        $usuario = $this->getUsuario();
        $raw = trim($request->bulk_data);

        // Try JSON parsing
        $booksList = json_decode($raw, true);

        // If not JSON, parse line-by-line format: "Título | Autor | Idioma (opcional) | Contenido o resumen"
        if (!is_array($booksList)) {
            $lines = preg_split('/\r\n|\r|\n/', $raw);
            $booksList = [];
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line) || str_starts_with($line, '#')) continue;

                $parts = explode('|', $line);
                if (count($parts) >= 2) {
                    $booksList[] = [
                        'titulo' => trim($parts[0]),
                        'autor' => trim($parts[1]),
                        'idioma_original_id' => 1,
                        'contenido' => isset($parts[2]) ? trim($parts[2]) : trim($parts[0] . ' por ' . $parts[1]),
                    ];
                }
            }
        }

        if (empty($booksList)) {
            return back()->withInput()->with('error', 'No se pudo detectar ninguna lista válida de libros. Por favor verifica el formato JSON o el formato separado por barras (|).');
        }

        try {
            $created = $llmService->processBulkBooksUpload($booksList, $request->target_idiomas, $usuario->id);
            return redirect()->route('dashboard.suscripciones')->with('success', '¡Se han procesado y traducido ' . count($created) . ' libros con IA exitosamente!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error durante la carga masiva: ' . $e->getMessage());
        }
    }

    public function suscripciones()
    {
        $usuario = $this->getUsuario();
        $suscripciones = Suscripcion::where('usuario_id', $usuario->id)
            ->with(['libro.idiomaOriginal', 'libro.ensenanzas', 'idiomas', 'estado'])
            ->get();

        $idiomas = CatIdioma::where('activo', true)->get();

        return view('dashboard.suscripciones', compact('suscripciones', 'idiomas', 'usuario'));
    }

    public function actualizarSuscripcion(Request $request, $id)
    {
        $usuario = $this->getUsuario();
        $sub = Suscripcion::where('usuario_id', $usuario->id)->findOrFail($id);

        $request->validate([
            'idiomas' => 'required|array|min:1',
            'idiomas.*' => 'exists:cat_idiomas,id',
        ]);

        $sub->idiomas()->sync($request->idiomas);

        return back()->with('success', 'Idiomas de la suscripción actualizados correctamente.');
    }

    public function pausarSuscripcion($id)
    {
        $usuario = $this->getUsuario();
        $sub = Suscripcion::where('usuario_id', $usuario->id)->findOrFail($id);

        if ($sub->estado_id == 1) {
            $sub->update(['estado_id' => 3]); // Pausado
            $msg = 'Suscripción pausada.';
        } else {
            $sub->update(['estado_id' => 1, 'fecha_proximo_envio' => Carbon::now()->addMinutes(5)]); // Activo
            $msg = 'Suscripción reactivada.';
        }

        return back()->with('success', $msg);
    }

    public function enviarPruebaEmail($id, LLMService $llmService)
    {
        $usuario = $this->getUsuario();
        $sub = Suscripcion::where('usuario_id', $usuario->id)->findOrFail($id);

        $command = new SendDailyTeachings();
        $enviado = $command->enviarEnsenanza($sub, $llmService);

        if ($enviado) {
            return back()->with('success', '¡Enseñanza enviada exitosamente a tu correo (' . $usuario->email . ')!');
        }

        return back()->with('info', 'El libro ya ha sido completado o no quedan más enseñanzas pendientes.');
    }

    public function leer($libroId, $orden = null)
    {
        $usuario = $this->getUsuario();
        $libro = Libro::with(['idiomaOriginal', 'ensenanzas.traducciones.idioma'])->findOrFail($libroId);

        $suscripcion = Suscripcion::where('usuario_id', $usuario->id)
            ->where('libro_id', $libroId)
            ->with('idiomas')
            ->first();

        $orden = $orden ? (int) $orden : ($suscripcion ? max(1, $suscripcion->ultima_ensenanza_enviada) : 1);
        $ensenanza = $libro->ensenanzas()->where('orden', $orden)->first() ?: $libro->ensenanzas()->first();

        $todosLosIdiomas = CatIdioma::where('activo', true)->get();

        return view('dashboard.leer', compact('libro', 'suscripcion', 'ensenanza', 'orden', 'todosLosIdiomas'));
    }

    public function sugerencias()
    {
        $usuario = $this->getUsuario();
        $sugerencias = SugerenciaUsuario::where('usuario_id', $usuario->id)
            ->orWhere('email', $usuario->email)
            ->with('tipo')
            ->orderBy('fecha_envio', 'desc')
            ->get();

        $tipos = CatTipoSugerencia::orderBy('orden')->get();

        return view('dashboard.sugerencias', compact('sugerencias', 'tipos'));
    }

    public function guardarSugerencia(Request $request)
    {
        $request->validate([
            'tipo_id' => 'required|exists:cat_tipos_sugerencia,id',
            'mensaje' => 'required|string|min:5|max:2000',
            'libro_sugerido' => 'nullable|string|max:255',
        ]);

        $usuario = $this->getUsuario();

        SugerenciaUsuario::create([
            'usuario_id' => $usuario->id,
            'email' => $usuario->email,
            'tipo_id' => $request->tipo_id,
            'libro_sugerido' => $request->libro_sugerido,
            'mensaje' => $request->mensaje,
            'leido' => false,
            'atendido' => false,
            'fecha_envio' => Carbon::now(),
        ]);

        return back()->with('success', '¡Tu sugerencia ha sido enviada con éxito!');
    }

    public function perfil()
    {
        $usuario = $this->getUsuario();
        $frecuencias = CatFrecuencia::orderBy('orden')->get();
        $planes = CatPlane::orderBy('orden')->get();

        return view('dashboard.perfil', compact('usuario', 'frecuencias', 'planes'));
    }

    public function actualizarPerfil(Request $request)
    {
        $usuario = $this->getUsuario();
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'hora_envio' => 'nullable|string',
            'zona_horaria' => 'nullable|string',
            'frecuencia_id' => 'required|exists:cat_frecuencias,id',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->update(['name' => $request->name]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        $usuario->update([
            'nombre' => $request->name,
            'hora_envio' => $request->hora_envio ?: '08:00:00',
            'zona_horaria' => $request->zona_horaria ?: 'America/Argentina/Buenos_Aires',
            'frecuencia_id' => $request->frecuencia_id,
        ]);

        return back()->with('success', 'Tus preferencias han sido actualizadas correctamente.');
    }
}