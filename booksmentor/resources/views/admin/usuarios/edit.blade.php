@extends('layouts.admin')

@section('title', 'Editar Usuario — Admin')
@section('breadcrumb', 'Editar Usuario')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <h1 class="text-2xl font-black text-slate-900">Editar Usuario: {{ $usuario->email }}</h1>

    <form action="{{ route('admin.usuarios.update', $usuario->id) }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4 text-xs">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Nombre Completo *</label>
                <input type="text" name="nombre" value="{{ old('nombre', $usuario->nombre ?: ($usuario->user ? $usuario->user->name : '')) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
            </div>

            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Rol en el Sistema</label>
                <select name="role" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
                    <option value="user" {{ $usuario->user && $usuario->user->role === 'user' ? 'selected' : '' }}>Usuario / Cliente</option>
                    <option value="admin" {{ $usuario->user && $usuario->user->role === 'admin' ? 'selected' : '' }}>Administrador</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Plan de Suscripción</label>
                <select name="plan_id" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
                    @foreach($planes as $p)
                        <option value="{{ $p->id }}" {{ $usuario->plan_id == $p->id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Frecuencia de Envío</label>
                <select name="frecuencia_id" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
                    @foreach($frecuencias as $f)
                        <option value="{{ $f->id }}" {{ $usuario->frecuencia_id == $f->id ? 'selected' : '' }}>{{ $f->nombre }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Nueva Contraseña (Opcional)</label>
                <input type="password" name="password" placeholder="Dejar en blanco para mantener actual" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
            </div>

            <div class="flex items-center pt-6 gap-2">
                <input type="checkbox" name="activo" value="1" {{ $usuario->activo ? 'checked' : '' }} class="rounded text-brand-600">
                <span class="font-bold text-slate-700">Cuenta Activa</span>
            </div>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl">
            Guardar Cambios
        </button>
    </form>
</div>
@endsection