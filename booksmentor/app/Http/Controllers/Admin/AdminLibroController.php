<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Libro;
use App\Models\Ensenanza;
use App\Models\Traduccion;
use App\Models\CatIdioma;
use App\Models\CatTag;
use App\Services\LLM\LLMService;
use Carbon\Carbon;

class AdminLibroController extends Controller
{
    public function index(Request $request)
    {
        $query = Libro::with(['idiomaOriginal', 'tags', 'ensenanzas']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('titulo', 'LIKE', "%{$buscar}%")
                  ->orWhere('autor', 'LIKE', "%{$buscar}%");
            });
        }

        if ($request->filled('idioma')) {
            $query->where('idioma_original_id', $request->idioma);
        }

        if ($request->filled('tag')) {
            $tagId = $request->tag;
            $query->whereHas('tags', function($q) use ($tagId) {
                $q->where('cat_tags.id', $tagId);
            });
        }

        $libros = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();
        $idiomas = CatIdioma::all();
        $tags = CatTag::all();

        return view('admin.libros.index', compact('libros', 'idiomas', 'tags'));
    }

    public function create()
    {
        $idiomas = CatIdioma::where('activo', true)->get();
        $tags = CatTag::all();

        return view('admin.libros.create', compact('idiomas', 'tags'));
    }

    public function store(Request $request, LLMService $llmService)
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
            'cantidad_ensenanzas' => 'nullable|integer|min:1|max:50',
            'tags' => 'nullable|array',
        ]);

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
                null,
                $count,
                $request->tags ?: []
            );

            return redirect()->route('admin.libros.show', $libro->id)->with('success', "¡Libro '{$libro->titulo}' creado y procesado exitosamente con {$libro->cantidad_ensenanzas} enseñanzas y sus traducciones!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al procesar el libro: ' . $e->getMessage());
        }
    }

    public function bulk()
    {
        $idiomas = CatIdioma::where('activo', true)->get();
        return view('admin.libros.bulk', compact('idiomas'));
    }

    public function storeBulk(Request $request, LLMService $llmService)
    {
        $request->validate([
            'bulk_data' => 'required|string',
            'target_idiomas' => 'required|array|min:1',
            'target_idiomas.*' => 'exists:cat_idiomas,id',
        ]);

        $raw = trim($request->bulk_data);
        $booksList = json_decode($raw, true);

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
            return back()->withInput()->with('error', 'Formato no reconocido. Usa JSON o líneas con Título | Autor | Contenido');
        }

        try {
            $created = $llmService->processBulkBooksUpload($booksList, $request->target_idiomas, null);
            return redirect()->route('admin.libros.index')->with('success', '¡Se han procesado ' . count($created) . ' libros con IA exitosamente!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error en carga masiva: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $libro = Libro::with(['idiomaOriginal', 'tags', 'ensenanzas.traducciones.idioma', 'creadoPor'])->findOrFail($id);
        $todosLosIdiomas = CatIdioma::where('activo', true)->get();

        return view('admin.libros.show', compact('libro', 'todosLosIdiomas'));
    }

    public function edit($id)
    {
        $libro = Libro::with('tags')->findOrFail($id);
        $idiomas = CatIdioma::all();
        $tags = CatTag::all();

        return view('admin.libros.edit', compact('libro', 'idiomas', 'tags'));
    }

    public function update(Request $request, $id)
    {
        $libro = Libro::findOrFail($id);

        $request->validate([
            'titulo' => 'required|string|max:255',
            'autor' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:1000',
            'portada_url' => 'nullable|url',
            'idioma_original_id' => 'required|exists:cat_idiomas,id',
            'anio_publicacion' => 'nullable|integer',
            'activo' => 'boolean',
            'tags' => 'nullable|array',
        ]);

        $libro->update([
            'titulo' => $request->titulo,
            'autor' => $request->autor,
            'descripcion' => $request->descripcion,
            'portada_url' => $request->portada_url,
            'idioma_original_id' => $request->idioma_original_id,
            'anio_publicacion' => $request->anio_publicacion,
            'activo' => $request->boolean('activo', true),
        ]);

        if ($request->has('tags')) {
            $libro->tags()->sync($request->tags);
        }

        return redirect()->route('admin.libros.show', $libro->id)->with('success', 'Libro actualizado correctamente.');
    }

    public function destroy($id)
    {
        $libro = Libro::findOrFail($id);
        $titulo = $libro->titulo;
        $libro->delete();

        return redirect()->route('admin.libros.index')->with('success', "Libro '{$titulo}' eliminado con éxito.");
    }

    public function toggleActivo($id)
    {
        $libro = Libro::findOrFail($id);
        $libro->activo = !$libro->activo;
        $libro->save();

        return back()->with('success', 'Estado del libro actualizado: ' . ($libro->activo ? 'Activo' : 'Inactivo'));
    }

    public function traducirFaltantes($id, LLMService $llmService)
    {
        $libro = Libro::with(['idiomaOriginal', 'ensenanzas.traducciones'])->findOrFail($id);
        $idiomas = CatIdioma::where('activo', true)->where('id', '!=', $libro->idioma_original_id)->get();
        $sourceCode = $libro->idiomaOriginal ? $libro->idiomaOriginal->codigo : 'es';

        $totalGeneradas = 0;

        foreach ($libro->ensenanzas as $ensenanza) {
            foreach ($idiomas as $idioma) {
                $existe = $ensenanza->traducciones->where('idioma_id', $idioma->id)->first();
                if (!$existe) {
                    $traducido = $llmService->translateText($ensenanza->texto_original, $sourceCode, $idioma->codigo);
                    Traduccion::create([
                        'ensenanza_id' => $ensenanza->id,
                        'idioma_id' => $idioma->id,
                        'texto_traducido' => $traducido,
                        'fecha_traduccion' => Carbon::now(),
                        'veces_usado' => 1,
                        'ultimo_uso' => Carbon::now(),
                    ]);
                    $totalGeneradas++;
                }
            }
        }

        return back()->with('success', "¡Se generaron {$totalGeneradas} traducciones faltantes con el LLM!");
    }
}