<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Libro;
use App\Models\Ensenanza;
use App\Models\Traduccion;
use App\Models\Suscripcion;
use App\Models\Usuario;
use App\Models\User;
use App\Models\SugerenciaUsuario;
use App\Models\HistorialEnvio;
use App\Models\CatIdioma;
use App\Services\LLM\LLMService;
use App\Console\Commands\SendDailyTeachings;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalLibros = Libro::count();
        $totalEnsenanzas = Ensenanza::count();
        $totalTraducciones = Traduccion::count();
        $totalSuscripciones = Suscripcion::where('estado_id', 1)->count();
        $totalUsuarios = Usuario::count();
        $sugerenciasPendientes = SugerenciaUsuario::where('leido', false)->orWhere('atendido', false)->count();

        $enviosHoy = HistorialEnvio::whereDate('fecha_envio', Carbon::today())->count();
        $enviosExitosos = HistorialEnvio::where('estado_id', 2)->count();
        $enviosFallidos = HistorialEnvio::where('estado_id', 5)->count();

        $ultimosLibros = Libro::with(['idiomaOriginal', 'tags'])->orderBy('created_at', 'desc')->take(5)->get();
        $ultimosUsuarios = Usuario::with(['plan', 'frecuencia'])->orderBy('created_at', 'desc')->take(5)->get();
        $ultimasSugerencias = SugerenciaUsuario::with('tipo')->orderBy('fecha_envio', 'desc')->take(5)->get();

        return view('admin.index', compact(
            'totalLibros',
            'totalEnsenanzas',
            'totalTraducciones',
            'totalSuscripciones',
            'totalUsuarios',
            'sugerenciasPendientes',
            'enviosHoy',
            'enviosExitosos',
            'enviosFallidos',
            'ultimosLibros',
            'ultimosUsuarios',
            'ultimasSugerencias'
        ));
    }

    public function configuracion()
    {
        $idiomas = CatIdioma::all();
        $currentProvider = config('services.llm.provider', env('LLM_DEFAULT_PROVIDER', 'deepseek'));
        $deepseekKey = env('DEEPSEEK_API_KEY');
        $deepseekUrl = env('DEEPSEEK_BASE_URL', 'https://api.deepseek.com/v1');
        $groqKey = env('GROQ_API_KEY');
        $openaiKey = env('OPENAI_API_KEY');
        $ollamaUrl = env('OLLAMA_BASE_URL', 'http://localhost:11434');

        return view('admin.configuracion', compact(
            'idiomas',
            'currentProvider',
            'deepseekKey',
            'deepseekUrl',
            'groqKey',
            'openaiKey',
            'ollamaUrl'
        ));
    }

    public function testLLM(Request $request, LLMService $llmService)
    {
        $provider = $request->input('provider', 'deepseek');
        $apiKey = $request->input('api_key');
        $baseUrl = $request->input('base_url');

        $result = $llmService->testConnection($provider, $apiKey, $baseUrl);
        return response()->json($result);
    }

    public function testTranslate(Request $request, LLMService $llmService)
    {
        $text = $request->input('texto', 'El cambio es la única constante.');
        $source = $request->input('source', 'es');
        $target = $request->input('target', 'en');

        $translated = $llmService->translateText($text, $source, $target);

        return response()->json([
            'success' => true,
            'original' => $text,
            'translated' => $translated,
            'source' => $source,
            'target' => $target
        ]);
    }

    public function ejecutarCron(LLMService $llmService)
    {
        $cmd = new SendDailyTeachings();
        
        $suscripciones = Suscripcion::where('estado_id', 1)->get();
        $enviados = 0;
        foreach ($suscripciones as $sub) {
            if ($cmd->enviarEnsenanza($sub, $llmService)) {
                $enviados++;
            }
        }

        return redirect()->back()->with('success', "¡Proceso de envío diario ejecutado! Se procesaron {$suscripciones->count()} suscripciones, enviados: {$enviados}.");
    }
}