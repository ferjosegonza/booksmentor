@extends('layouts.admin')

@section('title', 'Admin Dashboard — BooksMentor')
@section('breadcrumb', 'Dashboard Principal')

@section('content')
<div class="space-y-8">
    
    <!-- Top Action Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-xs">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Panel de Control General</h1>
            <p class="text-xs text-slate-500 mt-1">Supervisa libros, enseñanzas, traducciones IA, usuarios y el despachador de emails.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('admin.libros.create') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                + Crear Libro con IA
            </a>
            <a href="{{ route('admin.libros.bulk') }}" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                📦 Carga Masiva IA
            </a>
            <form action="{{ route('admin.ejecutarCron') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors" title="Ejecuta teachings:send manualmente ahora">
                    ⚡ Despachar Envíos Ahora
                </button>
            </form>
        </div>
    </div>

    <!-- Metrics Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Libros</span>
            <h3 class="text-2xl font-black text-slate-900">{{ $totalLibros }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Enseñanzas</span>
            <h3 class="text-2xl font-black text-slate-900">{{ $totalEnsenanzas }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Traducciones IA</span>
            <h3 class="text-2xl font-black text-brand-600">{{ $totalTraducciones }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Suscripciones</span>
            <h3 class="text-2xl font-black text-emerald-600">{{ $totalSuscripciones }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Usuarios</span>
            <h3 class="text-2xl font-black text-slate-900">{{ $totalUsuarios }}</h3>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Feedback Pendiente</span>
            <h3 class="text-2xl font-black {{ $sugerenciasPendientes > 0 ? 'text-amber-600' : 'text-slate-900' }}">{{ $sugerenciasPendientes }}</h3>
        </div>
    </div>

    <!-- Tables Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Recent Books -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900">Últimos Libros</h2>
                <a href="{{ route('admin.libros.index') }}" class="text-xs font-bold text-brand-600 hover:underline">Ver todos →</a>
            </div>

            <div class="space-y-3">
                @foreach($ultimosLibros as $lib)
                    <div class="p-3 bg-slate-50 rounded-2xl flex items-center justify-between">
                        <div>
                            <a href="{{ route('admin.libros.show', $lib->id) }}" class="text-xs font-bold text-slate-900 hover:text-brand-600">{{ $lib->titulo }}</a>
                            <span class="text-[11px] text-slate-500 block">por {{ $lib->autor }} · {{ $lib->cantidad_ensenanzas }} lecciones</span>
                        </div>
                        <a href="{{ route('admin.libros.show', $lib->id) }}" class="px-2.5 py-1 bg-white border border-slate-200 text-xs font-bold text-slate-700 rounded-lg hover:bg-slate-100">
                            Ver
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Suggestions -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900">Sugerencias y Mensajes Recientes</h2>
                <a href="{{ route('admin.sugerencias.index') }}" class="text-xs font-bold text-brand-600 hover:underline">Bandeja →</a>
            </div>

            <div class="space-y-3">
                @forelse($ultimasSugerencias as $sug)
                    <div class="p-3 bg-slate-50 rounded-2xl flex items-center justify-between">
                        <div class="max-w-xs sm:max-w-sm">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-slate-900">{{ $sug->email }}</span>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold {{ $sug->atendido ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                    {{ $sug->atendido ? 'Atendido' : 'Pendiente' }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 truncate mt-0.5">{{ $sug->mensaje }}</p>
                        </div>
                        <a href="{{ route('admin.sugerencias.show', $sug->id) }}" class="px-2.5 py-1 bg-brand-600 text-white text-xs font-bold rounded-lg hover:bg-brand-700">
                            Responder
                        </a>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">No hay sugerencias recientes.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection