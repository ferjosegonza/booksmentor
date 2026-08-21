<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Suscripcion;
use App\Models\Usuario;
use App\Models\Libro;
use App\Models\CatEstadoSuscripcion;
use App\Models\CatIdioma;
use App\Services\LLM\LLMService;
use App\Console\Commands\SendDailyTeachings;
use Carbon\Carbon;

class AdminSuscripcionController extends Controller
{
    public function index(Request $request)
    {
        $query = Suscripcion::with(['usuario', 'libro', 'estado', 'idiomas']);

        if ($request->filled('estado_id')) {
            $query->where('estado_id', $request->estado_id);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('usuario', function($q) use ($buscar) {
                $q->where('email', 'LIKE', "%{$buscar}%")->orWhere('nombre', 'LIKE', "%{$buscar}%");
            })->orWhereHas('libro', function($q) use ($buscar) {
                $q->where('titulo', 'LIKE', "%{$buscar}%");
            });
        }

        $suscripciones = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $estados = CatEstadoSuscripcion::all();

        return view('admin.suscripciones.index', compact('suscripciones', 'estados'));
    }

    public function update(Request $request, $id)
    {
        $sub = Suscripcion::findOrFail($id);

        $request->validate([
            'estado_id' => 'required|exists:cat_estados_suscripcion,id',
            'ultima_ensenanza_enviada' => 'required|integer|min:0',
        ]);

        $sub->update([
            'estado_id' => $request->estado_id,
            'ultima_ensenanza_enviada' => $request->ultima_ensenanza_enviada,
        ]);

        if ($request->has('idiomas')) {
            $sub->idiomas()->sync($request->idiomas);
        }

        return back()->with('success', 'Suscripción actualizada correctamente.');
    }

    public function forzarEnvio($id, LLMService $llmService)
    {
        $sub = Suscripcion::findOrFail($id);
        $cmd = new SendDailyTeachings();
        $ok = $cmd->enviarEnsenanza($sub, $llmService);

        if ($ok) {
            return back()->with('success', "¡Enseñanza enviada con éxito a {$sub->usuario->email}!");
        }

        return back()->with('info', "La suscripción no tiene más lecciones o se completó.");
    }

    public function destroy($id)
    {
        $sub = Suscripcion::findOrFail($id);
        $sub->delete();

        return back()->with('success', 'Suscripción eliminada.');
    }
}