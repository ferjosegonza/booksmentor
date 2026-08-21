@extends('layouts.admin')

@section('title', $libro->titulo . ' — Admin')
@section('breadcrumb', 'Detalle de Libro')

@section('content')
<div class="space-y-8">
    
    <!-- Header Card -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100">
            <div>
                <span class="text-xs text-slate-400">ID: #{{ $libro->id }} · Idioma Original: <strong>{{ $libro->idiomaOriginal ? $libro->idiomaOriginal->nombre : 'Español' }}</strong></span>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900">{{ $libro->titulo }}</h1>
                <p class="text-sm text-slate-500 font-medium">por {{ $libro->autor }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.ensenanzas.create', ['libro_id' => $libro->id]) }}" class="px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm">
                    + Añadir Lección
                </a>
                <form action="{{ route('admin.libros.traducirFaltantes', $libro->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm" title="Genera traducciones para todos los idiomas del catálogo con LLM">
                        🌐 Traducir Faltantes con IA
                    </button>
                </form>
                <a href="{{ route('admin.libros.edit', $libro->id) }}" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl">
                    Editar
                </a>
            </div>
        </div>

        <p class="text-xs text-slate-600 leading-relaxed">{{ $libro->descripcion }}</p>
    </div>

    <!-- Teachings and Translations Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden p-6 space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Lecciones y Estado de Traducciones ({{ $libro->ensenanzas->count() }})</h2>

        <div class="space-y-4">
            @foreach($libro->ensenanzas as $ens)
                <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-md bg-brand-600 text-white text-xs font-bold flex items-center justify-center">#{{ $ens->orden }}</span>
                            <h3 class="font-bold text-sm text-slate-900">{{ $ens->tema }}</h3>
                        </div>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.ensenanzas.edit', $ens->id) }}" class="text-xs font-semibold text-slate-600 hover:text-brand-600">Editar</a>
                            <span class="text-slate-300">·</span>
                            <form action="{{ route('admin.ensenanzas.destroy', $ens->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar esta lección?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Eliminar</button>
                            </form>
                        </div>
                    </div>

                    <!-- Original Text -->
                    <div class="text-xs text-slate-700 bg-white p-3 rounded-xl border border-slate-100">
                        <strong class="text-slate-900 block mb-1">Texto Original ({{ $libro->idiomaOriginal ? $libro->idiomaOriginal->codigo : 'es' }}):</strong>
                        {{ $ens->texto_original }}
                    </div>

                    <!-- Translations grid -->
                    <div class="space-y-1.5 pt-1">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Traducciones en Caché:</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs">
                            @foreach($ens->traducciones as $trad)
                                <div class="p-2.5 rounded-lg bg-white border border-slate-100 space-y-1">
                                    <div class="flex items-center justify-between">
                                        <span class="font-bold text-[11px] text-brand-700">{{ $trad->idioma ? $trad->idioma->nombre : 'Idioma' }} ({{ $trad->idioma ? strtoupper($trad->idioma->codigo) : '' }})</span>
                                        <span class="text-[9px] text-slate-400">Usado: {{ $trad->veces_usado }}x</span>
                                    </div>
                                    <p class="text-slate-600 text-[11px] leading-relaxed">{{ $trad->texto_traducido }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection