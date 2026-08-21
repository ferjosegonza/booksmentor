@extends('layouts.app')

@section('title', 'Mi Perfil y Preferencias — BooksMentor')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div>
        <h1 class="text-2xl font-black text-slate-900">⚙️ Mi Perfil y Preferencias</h1>
        <p class="text-xs text-slate-500 mt-1">Configura tus horarios de entrega de emails, frecuencia y contraseña.</p>
    </div>

    <!-- Active Plan Card -->
    <div class="bg-gradient-to-tr from-brand-600 to-indigo-600 text-white p-6 rounded-3xl shadow-lg flex items-center justify-between">
        <div>
            <span class="text-xs uppercase tracking-wider font-semibold opacity-80">Plan Actual</span>
            <h2 class="text-2xl font-black">{{ $usuario->plan ? $usuario->plan->nombre : 'Gratuito' }}</h2>
            <p class="text-xs opacity-90 mt-1">
                Límite: {{ $usuario->plan ? $usuario->plan->max_libros : 1 }} libros · {{ $usuario->plan ? $usuario->plan->max_idiomas : 1 }} idiomas
            </p>
        </div>

        <a href="{{ route('planes') }}" class="px-4 py-2 bg-white text-brand-700 font-bold text-xs rounded-xl hover:bg-slate-100 transition-colors">
            Cambiar Plan
        </a>
    </div>

    <!-- Preferences Form -->
    <form action="{{ route('dashboard.perfil.actualizar') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">Información Personal</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Nombre</label>
                    <input type="text" name="name" value="{{ old('name', $usuario->nombre ?: Auth::user()->name) }}" required class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Email</label>
                    <input type="email" disabled value="{{ Auth::user()->email }}" class="w-full px-3.5 py-2.5 text-xs bg-slate-50 border border-slate-200 rounded-xl text-slate-500 cursor-not-allowed">
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">Preferencias de Envío Diario</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Frecuencia de Envío</label>
                    <select name="frecuencia_id" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                        @foreach($frecuencias as $f)
                            <option value="{{ $f->id }}" {{ $usuario->frecuencia_id == $f->id ? 'selected' : '' }}>{{ $f->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Hora de Envío (Tu hora local)</label>
                    <input type="time" name="hora_envio" value="{{ substr($usuario->hora_envio ?? '08:00', 0, 5) }}" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                </div>
            </div>
        </div>

        <div class="space-y-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">Cambiar Contraseña (opcional)</h3>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Nueva Contraseña</label>
                    <input type="password" name="password" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Dejar en blanco para no cambiar">
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Repite la nueva contraseña">
                </div>
            </div>
        </div>

        <button type="submit" class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-md transition-colors">
            Guardar Cambios
        </button>
    </form>

</div>
@endsection