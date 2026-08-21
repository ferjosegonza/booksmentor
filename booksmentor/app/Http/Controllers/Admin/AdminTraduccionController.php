<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Traduccion;
use App\Models\Ensenanza;
use App\Models\CatIdioma;
use App\Models\Libro;
use App\Services\LLM\LLMService;
use Carbon\Carbon;

class AdminTraduccionController extends Controller
{
    public function index(Request $request)
    {
        $query = Traduccion::with(['ensenanza.libro', 'idioma']);

        if ($request->filled('idioma_id')) {
            $query->where('idioma_id', $request->idioma_id);
        }

        if ($request->filled('libro_id')) {
            $libroId = $request->libro_id;
            $query->whereHas('ensenanza', function($q) use ($libroId) {
                $q->where('libro_id', $libroId);
            });
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('texto_traducido', 'LIKE', "%{$buscar}%");
        }

        $traducciones = $query->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();
        $idiomas = CatIdioma::all();
        $libros = Libro::orderBy('titulo')->get();

        return view('admin.traducciones.index', compact('traducciones', 'idiomas', 'libros'));
    }

    public function edit($id)
    {
        $traduccion = Traduccion::with(['ensenanza.libro', 'idioma'])->findOrFail($id);
        return view('admin.traducciones.edit', compact('traduccion'));
    }

    public function update(Request $request, $id)
    {
        $traduccion = Traduccion::findOrFail($id);
        $request->validate(['texto_traducido' => 'required|string|min:5']);

        $traduccion->update(['texto_traducido' => $request->texto_traducido]);

        return redirect()->route('admin.traducciones.index')->with('success', 'Traducción actualizada con éxito.');
    }

    public function destroy($id)
    {
        $traduccion = Traduccion::findOrFail($id);
        $traduccion->delete();

        return back()->with('success', 'Traducción eliminada del caché.');
    }

    public function regenerar($id, LLMService $llmService)
    {
        $traduccion = Traduccion::with(['ensenanza.libro.idiomaOriginal', 'idioma'])->findOrFail($id);
        $ensenanza = $traduccion->ensenanza;
        $srcCode = $ensenanza->libro->idiomaOriginal ? $ensenanza->libro->idiomaOriginal->codigo : 'es';

        $nuevoTexto = $llmService->translateText($ensenanza->texto_original, $srcCode, $traduccion->idioma->codigo);
        $traduccion->update([
            'texto_traducido' => $nuevoTexto,
            'fecha_traduccion' => Carbon::now(),
            'ultimo_uso' => Carbon::now(),
        ]);

        return back()->with('success', 'Traducción regenerada exitosamente con el LLM.');
    }
}