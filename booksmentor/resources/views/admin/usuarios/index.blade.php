@extends('layouts.admin')

@section('title', 'Usuarios — Admin')
@section('breadcrumb', 'Usuarios Registrados')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">👥 Usuarios Registrados</h1>
            <p class="text-xs text-slate-500 mt-1">Administra cuentas, planes asignados y roles del sistema.</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-100">
                <tr>
                    <th class="py-3 px-4">Usuario</th>
                    <th class="py-3 px-4">Plan</th>
                    <th class="py-3 px-4">Frecuencia</th>
                    <th class="py-3 px-4">Rol</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($usuarios as $u)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-900 block text-sm">{{ $u->nombre ?: ($u->user ? $u->user->name : 'Usuario') }}</span>
                            <span class="text-xs text-slate-400">{{ $u->email }}</span>
                        </td>
                        <td class="py-3.5 px-4 font-semibold text-brand-700">{{ $u->plan ? $u->plan->nombre : '-' }}</td>
                        <td class="py-3.5 px-4">{{ $u->frecuencia ? $u->frecuencia->nombre : '-' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $u->user && $u->user->role === 'admin' ? 'bg-indigo-100 text-indigo-800' : 'bg-slate-100 text-slate-700' }}">
                                {{ $u->user ? $u->user->role : 'user' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $u->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $u->activo ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1">
                            <a href="{{ route('admin.usuarios.edit', $u->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-semibold">Editar</a>
                            <form action="{{ route('admin.usuarios.destroy', $u->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2.5 py-1 bg-rose-50 text-rose-700 rounded text-[11px]">✕</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">{{ $usuarios->links() }}</div>
    </div>
</div>
@endsection