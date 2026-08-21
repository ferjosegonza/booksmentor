<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\User;
use App\Models\CatPlane;
use App\Models\CatFrecuencia;
use Illuminate\Support\Facades\Hash;

class AdminUsuarioController extends Controller
{
    public function index(Request $request)
    {
        $query = Usuario::with(['user', 'plan', 'frecuencia', 'suscripciones.libro']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where('email', 'LIKE', "%{$buscar}%")
                  ->orWhere('nombre', 'LIKE', "%{$buscar}%");
        }

        if ($request->filled('plan_id')) {
            $query->where('plan_id', $request->plan_id);
        }

        $usuarios = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $planes = CatPlane::all();
        $frecuencias = CatFrecuencia::all();

        return view('admin.usuarios.index', compact('usuarios', 'planes', 'frecuencias'));
    }

    public function edit($id)
    {
        $usuario = Usuario::with('user')->findOrFail($id);
        $planes = CatPlane::all();
        $frecuencias = CatFrecuencia::all();

        return view('admin.usuarios.edit', compact('usuario', 'planes', 'frecuencias'));
    }

    public function update(Request $request, $id)
    {
        $usuario = Usuario::findOrFail($id);

        $request->validate([
            'nombre' => 'required|string|max:255',
            'plan_id' => 'required|exists:cat_planes,id',
            'frecuencia_id' => 'required|exists:cat_frecuencias,id',
            'hora_envio' => 'nullable|string',
            'zona_horaria' => 'nullable|string',
            'role' => 'nullable|in:user,admin',
            'password' => 'nullable|string|min:6',
        ]);

        $usuario->update([
            'nombre' => $request->nombre,
            'plan_id' => $request->plan_id,
            'frecuencia_id' => $request->frecuencia_id,
            'hora_envio' => $request->hora_envio ?: '08:00:00',
            'zona_horaria' => $request->zona_horaria ?: 'America/Argentina/Buenos_Aires',
            'activo' => $request->boolean('activo', true),
        ]);

        if ($usuario->user) {
            $userData = ['name' => $request->nombre];
            if ($request->filled('role')) {
                $userData['role'] = $request->role;
            }
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }
            $usuario->user->update($userData);
        }

        return redirect()->route('admin.usuarios.index')->with('success', 'Usuario actualizado con éxito.');
    }

    public function destroy($id)
    {
        $usuario = Usuario::findOrFail($id);
        if ($usuario->user) {
            $usuario->user->delete();
        }
        $usuario->delete();

        return back()->with('success', 'Usuario eliminado.');
    }
}