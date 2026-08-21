<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CatPlane;
use App\Models\CatFrecuencia;
use App\Models\CatIdioma;
use App\Models\CatTag;
use App\Models\CatTipoSugerencia;

class AdminCatalogoController extends Controller
{
    public function index()
    {
        $planes = CatPlane::orderBy('orden')->get();
        $frecuencias = CatFrecuencia::orderBy('orden')->get();
        $idiomas = CatIdioma::all();
        $tags = CatTag::all();
        $tiposSugerencia = CatTipoSugerencia::orderBy('orden')->get();

        return view('admin.catalogos.index', compact('planes', 'frecuencias', 'idiomas', 'tags', 'tiposSugerencia'));
    }

    public function storeTag(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:cat_tags,slug',
            'icono' => 'nullable|string|max:20',
        ]);

        CatTag::create($request->only('nombre', 'slug', 'icono'));
        return back()->with('success', 'Etiqueta creada con éxito.');
    }

    public function storeIdioma(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'codigo' => 'required|string|max:10|unique:cat_idiomas,codigo',
        ]);

        CatIdioma::create([
            'nombre' => $request->nombre,
            'codigo' => $request->codigo,
            'activo' => true,
        ]);

        return back()->with('success', 'Idioma agregado al catálogo.');
    }

    public function toggleIdioma($id)
    {
        $idioma = CatIdioma::findOrFail($id);
        $idioma->activo = !$idioma->activo;
        $idioma->save();

        return back()->with('success', 'Estado del idioma modificado.');
    }
}