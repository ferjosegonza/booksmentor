<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SugerenciaUsuario;
use App\Models\CatTipoSugerencia;
use Carbon\Carbon;

class AdminSugerenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = SugerenciaUsuario::with(['usuario', 'tipo']);

        if ($request->filled('tipo_id')) {
            $query->where('tipo_id', $request->tipo_id);
        }

        if ($request->filled('estado')) {
            if ($request->estado === 'pendientes') {
                $query->where('atendido', false);
            } elseif ($request->estado === 'no_leidas') {
                $query->where('leido', false);
            } elseif ($request->estado === 'atendidas') {
                $query->where('atendido', true);
            }
        }

        $sugerencias = $query->orderBy('fecha_envio', 'desc')->paginate(20)->withQueryString();
        $tipos = CatTipoSugerencia::all();

        return view('admin.sugerencias.index', compact('sugerencias', 'tipos'));
    }

    public function show($id)
    {
        $sugerencia = SugerenciaUsuario::with(['usuario', 'tipo'])->findOrFail($id);
        if (!$sugerencia->leido) {
            $sugerencia->update(['leido' => true]);
        }

        return view('admin.sugerencias.show', compact('sugerencia'));
    }

    public function responder(Request $request, $id)
    {
        $sugerencia = SugerenciaUsuario::findOrFail($id);

        $request->validate(['respuesta_admin' => 'required|string|min:5']);

        $sugerencia->update([
            'respuesta_admin' => $request->respuesta_admin,
            'atendido' => true,
            'leido' => true,
            'fecha_respuesta' => Carbon::now(),
        ]);

        return redirect()->route('admin.sugerencias.index')->with('success', 'Respuesta guardada y sugerencia marcada como atendida.');
    }

    public function destroy($id)
    {
        $sugerencia = SugerenciaUsuario::findOrFail($id);
        $sugerencia->delete();

        return back()->with('success', 'Sugerencia eliminada.');
    }
}