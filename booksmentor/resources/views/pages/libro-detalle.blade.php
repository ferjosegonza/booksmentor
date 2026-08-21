@extends('layouts.public')

@section('title', $libro->titulo . ' — BooksMentor')

@section('content')
<div class="bg-slate-50 py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                <div class="md:col-span-8 space-y-4">
                    <div class="flex items-center gap-2">
                        @foreach($libro->tags as $t)
                            <span class="px-2.5 py-1 rounded-md text-xs font-semibold bg-brand-50 text-brand-700">{{ $t->icono }} {{ $t->nombre }}</span>
                        @endforeach
                        <span class="text-xs text-slate-400">Año: {{ $libro->anio_publicacion ?: 'N/A' }}</span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900">{{ $libro->titulo }}</h1>
                    <p class="text-lg text-slate-600 font-medium">por {{ $libro->autor }}</p>

                    <p class="text-slate-700 leading-relaxed pt-2">
                        {{ $libro->descripcion }}
                    </p>

                    <div class="pt-4 flex flex-wrap items-center gap-4 text-xs font-semibold text-slate-500">
                        <span>Idioma original: <strong>{{ $libro->idiomaOriginal ? $libro->idiomaOriginal->nombre : 'Español' }}</strong></span>
                        <span>·</span>
                        <span>Total Lecciones: <strong>{{ $libro->cantidad_ensenanzas }}</strong></span>
                    </div>
                </div>

                <div class="md:col-span-4 bg-slate-50 p-6 rounded-2xl border border-slate-200 space-y-4">
                    <h3 class="font-bold text-slate-900 text-sm">Suscribirme a este libro</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">Recibirás una lección diaria en tu correo en tus idiomas seleccionados.</p>

                    @auth
                        <form action="{{ route('dashboard.suscribir') }}" method="POST" class="space-y-3">
                            @csrf
                            <input type="hidden" name="libro_id" value="{{ $libro->id }}">
                            
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Elegir Idiomas</label>
                                <div class="space-y-1 text-xs">
                                    @foreach($idiomas as $lang)
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="idiomas[]" value="{{ $lang->id }}" {{ $loop->first ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                                            <span>{{ $lang->nombre }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                                Comenzar a recibir
                            </button>
                        </form>
                    @else
                        <a href="{{ route('register', ['libro_id' => $libro->id]) }}" class="block w-full py-2.5 text-center bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                            Registrarme y Suscribirme Gratis
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Teachings Preview -->
        <div class="bg-white rounded-3xl p-6 sm:p-10 shadow-sm border border-slate-200">
            <h2 class="text-2xl font-bold text-slate-900 mb-6">Lecciones Incluidas ({{ $libro->ensenanzas->count() }})</h2>
            
            <div class="space-y-4">
                @foreach($libro->ensenanzas as $ensenanza)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-start gap-4">
                        <span class="w-8 h-8 rounded-lg bg-brand-100 text-brand-700 font-bold flex items-center justify-center text-xs shrink-0">
                            #{{ $ensenanza->orden }}
                        </span>
                        <div class="space-y-1">
                            <h4 class="font-bold text-slate-900 text-sm">{{ $ensenanza->tema }}</h4>
                            <p class="text-xs text-slate-600 leading-relaxed">{{ $ensenanza->texto_original }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
@endsection