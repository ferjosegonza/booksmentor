@extends('layouts.public')

@section('title', 'Recuperar Contraseña — BooksMentor')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-md w-full space-y-6 bg-white p-8 rounded-3xl shadow-xl border border-slate-100 text-center">
        <h2 class="text-2xl font-black text-slate-900">Restablecer Contraseña</h2>
        <p class="text-sm text-slate-500">Ingresa tu correo y te enviaremos las instrucciones de recuperación.</p>

        @if(session('status'))
            <div class="p-3 bg-emerald-50 text-emerald-800 text-xs rounded-xl border border-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4 text-left">
            @csrf
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Correo Electrónico</label>
                <input type="email" name="email" required class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="tu@email.com">
            </div>

            <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-md transition-colors">
                Enviar enlace de recuperación
            </button>
        </form>

        <div class="text-xs text-slate-500">
            <a href="{{ route('login') }}" class="text-brand-600 font-bold hover:underline">← Volver al inicio de sesión</a>
        </div>
    </div>
</div>
@endsection