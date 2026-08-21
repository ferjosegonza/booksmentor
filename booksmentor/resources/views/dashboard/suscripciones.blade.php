@extends('layouts.app')

@section('title', 'Mis Suscripciones — BooksMentor')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">📚 Mis Libros y Suscripciones</h1>
            <p class="text-xs text-slate-500 mt-1">Administra tus libros en seguimiento, cambia idiomas de traducción o envía una lección de prueba.</p>
        </div>
        <a href="{{ route('dashboard.explorar') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors text-center">
            + Suscribirme a otro libro
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6">
        @forelse($suscripciones as $sub)
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                
                <div class="space-y-2 flex-grow max-w-xl">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold {{ $sub->estado_id == 1 ? 'bg-emerald-50 text-emerald-700' : ($sub->estado_id == 2 ? 'bg-indigo-50 text-indigo-700' : 'bg-amber-50 text-amber-700') }}">
                            {{ $sub->estado ? $sub->estado->nombre : 'Activo' }}
                        </span>
                        <span class="text-xs text-slate-400">·</span>
                        <span class="text-xs text-slate-500 font-semibold">Lección {{ $sub->ultima_ensenanza_enviada }} de {{ $sub->libro->cantidad_ensenanzas }}</span>
                        <span class="text-xs font-bold text-brand-600">({{ $sub->porcentaje_avance }}%)</span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900">{{ $sub->libro->titulo }}</h3>
                    <p class="text-xs text-slate-500 font-medium">por {{ $sub->libro->autor }}</p>

                    <!-- Progress bar -->
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden my-2">
                        <div class="bg-brand-600 h-2 rounded-full transition-all" style="width: {{ $sub->porcentaje_avance }}%"></div>
                    </div>

                    <!-- Idiomas actuales -->
                    <div class="flex flex-wrap items-center gap-1.5 pt-1">
                        <span class="text-[10px] font-bold uppercase text-slate-400">Idiomas:</span>
                        @foreach($sub->idiomas as $l)
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded text-[11px] font-semibold">
                                {{ $l->nombre }} ({{ strtoupper($l->codigo) }})
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap md:flex-col items-center gap-2.5 w-full md:w-auto shrink-0">
                    <a href="{{ route('dashboard.leer', [$sub->libro->id, max(1, $sub->ultima_ensenanza_enviada)]) }}" class="w-full sm:w-auto px-4 py-2 bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs rounded-xl text-center transition-colors">
                        📖 Leer Lecciones
                    </a>

                    <form action="{{ route('dashboard.suscripciones.enviarPrueba', $sub->id) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors">
                            ✉️ Enviar a mi email
                        </button>
                    </form>

                    <form action="{{ route('dashboard.suscripciones.pausar', $sub->id) }}" method="POST" class="w-full sm:w-auto">
                        @csrf
                        <button type="submit" class="w-full px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-700 transition-colors">
                            {{ $sub->estado_id == 1 ? '⏸ Pausar envíos' : '▶ Reanudar envíos' }}
                        </button>
                    </form>
                </div>

            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-3xl border border-slate-200">
                <p class="text-slate-500 text-sm mb-4">Aún no te has suscrito a ningún libro.</p>
                <a href="{{ route('dashboard.explorar') }}" class="px-6 py-3 bg-brand-600 text-white font-bold text-xs rounded-xl shadow-md">
                    Explorar y Suscribirme a un Libro
                </a>
            </div>
        @endforelse
    </div>

</div>
@endsection