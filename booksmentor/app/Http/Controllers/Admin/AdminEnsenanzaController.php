<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ensenanza;
use App\Models\Libro;
use App\Models\Traduccion;
use App\Models\CatIdioma;
use App\Services\LLM\LLMService;
use Carbon\Carbon;

class AdminEnsenanzaController extends Controller
{
    public function index(Request $request)
    {
        $query = Ensenanza::with(['libro.idiomaOriginal', 'traducciones.idioma']);

        if ($request->filled('libro_id')) {
            $query->where('libro_id', $request->libro_id);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('tema', 'LIKE', "%{$buscar}%")
                  ->orWhere('texto_original', 'LIKE', "%{$buscar}%");
            });
        }

        $ensenanzas = $query->orderBy('libro_id')->orderBy('orden')->paginate(20)->withQueryString();
        $libros = Libro::orderBy('titulo')->get();

        return view('admin.ensenanzas.index', compact('ensenanzas', 'libros'));
    }

    public function create(Request $request)
    {
        $libros = Libro::orderBy('titulo')->get();
        $selectedLibroId = $request->libro_id;
        $siguienteOrden = 1;

        if ($selectedLibroId) {
            $max = Ensenanza::where('libro_id', $selectedLibroId)->max('orden');
            $siguienteOrden = ($max ?? 0) + 1;
        }

        return view('admin.ensenanzas.create', compact('libros', 'selectedLibroId', 'siguienteOrden'));
    }

    public function store(Request $request, LLMService $llmService)
    {
        $request->validate([
            'libro_id' => 'required|exists:libros,id',
            'orden' => 'required|integer|min:1',
            'tema' => 'required|string|max:255',
            'texto_original' => 'required|string|min:10',
            'auto_traducir' => 'nullable|boolean',
        ]);

        $ensenanza = Ensenanza::create([
            'libro_id' => $request->libro_id,
            'orden' => $request->orden,
            'tema' => $request->tema,
            'texto_original' => $request->texto_original,
        ]);

        // Update book count
        $libro = Libro::find($request->libro_id);
        $libro->cantidad_ensenanzas = Ensenanza::where('libro_id', $libro->id)->count();
        $libro->save();

        if ($request->boolean('auto_traducir', true)) {
            $idiomas = CatIdioma::where('activo', true)->where('id', '!=', $libro->idioma_original_id)->get();
            $srcCode = $libro->idiomaOriginal ? $libro->idiomaOriginal->codigo : 'es';

            foreach ($idiomas as $idioma) {
                $trad = $llmService->translateText($ensenanza->texto_original, $srcCode, $idioma->codigo);
                Traduccion::create([
                    'ensenanza_id' => $ensenanza->id,
                    'idioma_id' => $idioma->id,
                    'texto_traducido' => $trad,
                    'fecha_traduccion' => Carbon::now(),
                    'veces_usado' => 1,
                    'ultimo_uso' => Carbon::now(),
                ]);
            }
        }

        return redirect()->route('admin.libros.show', $request->libro_id)->with('success', 'Enseñanza creada y traducida exitosamente.');
    }

    public function edit($id)
    {
        $ensenanza = Ensenanza::with(['libro', 'traducciones.idioma'])->findOrFail($id);
        $idiomas = CatIdioma::where('activo', true)->get();

        return view('admin.ensenanzas.edit', compact('ensenanza', 'idiomas'));
    }

    public function update(Request $request, $id)
    {
        $ensenanza = Ensenanza::findOrFail($id);

        $request->validate([
            'orden' => 'required|integer|min:1',
            'tema' => 'required|string|max:255',
            'texto_original' => 'required|string|min:10',
        ]);

        $ensenanza->update([
            'orden' => $request->orden,
            'tema' => $request->tema,
            'texto_original' => $request->texto_original,
        ]);

        return redirect()->route('admin.libros.show', $ensenanza->libro_id)->with('success', 'Enseñanza actualizada.');
    }

    public function destroy($id)
    {
        $ensenanza = Ensenanza::findOrFail($id);
        $libroId = $ensenanza->libro_id;
        $ensenanza->delete();

        $libro = Libro::find($libroId);
        if ($libro) {
            $libro->cantidad_ensenanzas = Ensenanza::where('libro_id', $libroId)->count();
            $libro->save();
        }

        return redirect()->route('admin.libros.show', $libroId)->with('success', 'Enseñanza eliminada.');
    }

    public function traducir($id, Request $request, LLMService $llmService)
    {
        $ensenanza = Ensenanza::with('libro.idiomaOriginal')->findOrFail($id);
        $idiomaId = $request->input('idioma_id');
        $idioma = CatIdioma::findOrFail($idiomaId);

        $srcCode = $ensenanza->libro->idiomaOriginal ? $ensenanza->libro->idiomaOriginal->codigo : 'es';
        $trad = $llmService->translateText($ensenanza->texto_original, $srcCode, $idioma->codigo);

        $traduccion = Traduccion::updateOrCreate(
            ['ensenanza_id' => $ensenanza->id, 'idioma_id' => $idioma->id],
            [
                'texto_traducido' => $trad,
                'fecha_traduccion' => Carbon::now(),
                'ultimo_uso' => Carbon::now(),
            ]
        );

        return back()->with('success', "Traducción al {$idioma->nombre} generada/actualizada con éxito.");
    }
}