<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use App\Models\Libro;
use App\Models\CatPlane;
use App\Models\CatIdioma;
use App\Models\CatTag;
use App\Models\SugerenciaUsuario;
use App\Models\CatTipoSugerencia;
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $librosDestacados = Libro::where('activo', true)
            ->with(['idiomaOriginal', 'tags', 'ensenanzas.traducciones'])
            ->take(6)
            ->get();

        $planes = CatPlane::orderBy('orden')->get();
        $idiomas = CatIdioma::where('activo', true)->get();
        $tags = CatTag::all();
        $tiposSugerencia = CatTipoSugerencia::orderBy('orden')->get();

        return view('welcome', compact('librosDestacados', 'planes', 'idiomas', 'tags', 'tiposSugerencia'));
    }

    public function switchLanguage($lang)
    {
        $supported = ['es', 'en', 'pt', 'it', 'fr', 'zh', 'zh-TW', 'de'];
        if (in_array($lang, $supported)) {
            Session::put('locale', $lang);
            App::setLocale($lang);
        }
        return redirect()->back();
    }

    public function donaciones()
    {
        return view('pages.donaciones');
    }

    public function planes()
    {
        $planes = CatPlane::orderBy('orden')->get();
        return view('pages.planes', compact('planes'));
    }

    public function explorar(Request $request)
    {
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

        if ($request->filled('idioma')) {
            $idiomaId = $request->idioma;
            $query->where('idioma_original_id', $idiomaId);
        }

        $libros = $query->paginate(12)->withQueryString();
        $tags = CatTag::all();
        $idiomas = CatIdioma::where('activo', true)->get();

        return view('pages.explorar', compact('libros', 'tags', 'idiomas'));
    }

    public function showLibro($id)
    {
        $libro = Libro::with(['idiomaOriginal', 'tags', 'ensenanzas.traducciones.idioma'])->findOrFail($id);
        $idiomas = CatIdioma::where('activo', true)->get();

        return view('pages.libro-detalle', compact('libro', 'idiomas'));
    }

    public function storeSugerencia(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'tipo_id' => 'required|exists:cat_tipos_sugerencia,id',
            'mensaje' => 'required|string|min:5|max:2000',
            'libro_sugerido' => 'nullable|string|max:255',
        ]);

        $usuarioId = null;
        if (Auth::check()) {
            $usuario = Auth::user()->usuario;
            $usuarioId = $usuario ? $usuario->id : null;
        }

        SugerenciaUsuario::create([
            'usuario_id' => $usuarioId,
            'email' => $request->email,
            'tipo_id' => $request->tipo_id,
            'libro_sugerido' => $request->libro_sugerido,
            'mensaje' => $request->mensaje,
            'leido' => false,
            'atendido' => false,
            'fecha_envio' => Carbon::now(),
        ]);

        return redirect()->back()->with('success', '¡Muchas gracias por tu sugerencia! La revisaremos a la brevedad.');
    }
}