@extends('layouts.public')

@section('title', 'Registro y Onboarding — BooksMentor')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8 bg-slate-50">
    <div class="max-w-2xl mx-auto bg-white p-6 sm:p-10 rounded-3xl shadow-xl border border-slate-100">
        
        <div class="text-center mb-8">
            <div class="w-12 h-12 rounded-2xl bg-brand-600 flex items-center justify-center text-white font-bold text-xl mx-auto shadow-md shadow-brand-500/30">
                BM
            </div>
            <h2 class="mt-4 text-3xl font-black text-slate-900 tracking-tight">Comienza tu viaje de aprendizaje</h2>
            <p class="mt-2 text-sm text-slate-500">Configura tus preferencias y recibe tu primera lección hoy mismo.</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Step 1: Account Info -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">1. Datos de tu Cuenta</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Nombre Completo *</label>
                        <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Ej: María González">
                        @error('name') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Correo Electrónico *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="tu@email.com">
                        @error('email') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Contraseña *</label>
                        <input type="password" name="password" required class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Mínimo 6 caracteres">
                        @error('password') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Confirmar Contraseña *</label>
                        <input type="password" name="password_confirmation" required class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Repite tu contraseña">
                    </div>
                </div>
            </div>

            <!-- Step 2: Plan Selection -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">2. Elige tu Plan</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($planes as $p)
                        <label class="relative flex p-3.5 rounded-xl border border-slate-200 hover:border-brand-500 cursor-pointer transition-colors has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/40">
                            <input type="radio" name="plan_id" value="{{ $p->id }}" {{ ($p->id == request('plan_id', 1)) ? 'checked' : '' }} class="text-brand-600 focus:ring-brand-500 mt-0.5">
                            <div class="ml-3">
                                <span class="block text-sm font-bold text-slate-900">{{ $p->nombre }} ({{ $p->precio_mensual > 0 ? '$'.$p->precio_mensual.'/mes' : 'Gratis' }})</span>
                                <span class="block text-xs text-slate-500">{{ $p->max_libros >= 999 ? 'Ilimitados' : $p->max_libros }} libros · {{ $p->max_idiomas }} idiomas</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- Step 3: Delivery Preferences -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">3. Frecuencia y Horario de Envío</h3>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Frecuencia</label>
                        <select name="frecuencia_id" class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                            @foreach($frecuencias as $f)
                                <option value="{{ $f->id }}" {{ $f->id == 1 ? 'selected' : '' }}>{{ $f->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Hora Preferida (hora local)</label>
                        <input type="time" name="hora_envio" value="08:00" class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    </div>
                </div>
            </div>

            <!-- Step 4: First Book & Languages -->
            <div class="space-y-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2">4. Tu Primer Libro e Idiomas de Traducción</h3>
                
                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Seleccionar Libro Inicial</label>
                    <select name="libro_id" class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                        @foreach($librosDestacados as $lib)
                            <option value="{{ $lib->id }}" {{ $lib->id == request('libro_id') ? 'selected' : '' }}>
                                {{ $lib->titulo }} (por {{ $lib->autor }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase text-slate-700 mb-2">Idiomas en los que deseas recibir las lecciones:</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach($idiomas as $lang)
                            <label class="flex items-center gap-2 p-2 rounded-lg border border-slate-200 text-xs font-medium cursor-pointer hover:bg-slate-50">
                                <input type="checkbox" name="idiomas[]" value="{{ $lang->id }}" {{ in_array($lang->codigo, ['es', 'en']) ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                                <span>{{ $lang->nombre }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <button type="submit" class="w-full py-3.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-base rounded-xl shadow-lg shadow-brand-500/25 transition-all">
                Completar Registro y Comenzar
            </button>
        </form>

        <div class="text-center text-xs text-slate-500 mt-6">
            ¿Ya tienes cuenta? 
            <a href="{{ route('login') }}" class="font-bold text-brand-600 hover:text-brand-700">Inicia sesión</a>
        </div>

    </div>
</div>
@endsection