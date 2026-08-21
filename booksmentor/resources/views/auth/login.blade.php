@extends('layouts.public')

@section('title', 'Iniciar Sesión — BooksMentor')

@section('content')
<div class="min-h-[75vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-100">
        
        <div class="text-center">
            <div class="w-12 h-12 rounded-2xl bg-brand-600 flex items-center justify-center text-white font-bold text-xl mx-auto shadow-md shadow-brand-500/30">
                BM
            </div>
            <h2 class="mt-4 text-3xl font-black text-slate-900 tracking-tight">Iniciar Sesión</h2>
            <p class="mt-2 text-sm text-slate-500">Accede a tus suscripciones y lecciones diarias</p>
        </div>

        <!-- Quick Demo Credentials Auto-Fill Box -->
        <div class="p-3.5 bg-slate-50 rounded-2xl border border-slate-200 text-xs text-slate-600 space-y-2">
            <span class="font-bold text-slate-700 block uppercase tracking-wider text-[10px]">Accesos Rápidos de Prueba:</span>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" onclick="fillCredentials('admin@booksmentor.com', 'admin12345')" class="py-1.5 px-2.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-slate-800 font-semibold shadow-xs transition-colors text-left">
                    👑 <span class="text-brand-600 font-bold">Admin Demo</span>
                </button>
                <button type="button" onclick="fillCredentials('usuario@booksmentor.com', 'usuario12345')" class="py-1.5 px-2.5 bg-white hover:bg-slate-100 border border-slate-200 rounded-lg text-slate-800 font-semibold shadow-xs transition-colors text-left">
                    👤 <span class="text-indigo-600 font-bold">Usuario Demo</span>
                </button>
            </div>
        </div>

        <form class="mt-6 space-y-5" action="{{ route('login') }}" method="POST">
            @csrf
            <div>
                <label for="email" class="block text-xs font-bold uppercase text-slate-700 mb-1">Correo Electrónico</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}" class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="tu@email.com">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-xs font-bold uppercase text-slate-700">Contraseña</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-brand-600 hover:text-brand-700 font-medium">¿Olvidaste tu contraseña?</a>
                </div>
                <input id="password" name="password" type="password" required class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="••••••••">
            </div>

            <div class="flex items-center">
                <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-brand-600 focus:ring-brand-500 border-slate-300 rounded">
                <label for="remember" class="ml-2 block text-xs font-medium text-slate-600">Recordarme en este dispositivo</label>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-xl shadow-lg shadow-brand-500/25 transition-all">
                Iniciar Sesión
            </button>
        </form>

        <div class="text-center text-xs text-slate-500 pt-2">
            ¿Aún no tienes cuenta? 
            <a href="{{ route('register') }}" class="font-bold text-brand-600 hover:text-brand-700">Regístrate gratis aquí</a>
        </div>

    </div>
</div>

<script>
    function fillCredentials(email, pass) {
        document.getElementById('email').value = email;
        document.getElementById('password').value = pass;
    }
</script>
@endsection