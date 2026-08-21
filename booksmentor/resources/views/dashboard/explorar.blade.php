@extends('layouts.app')

@section('title', 'Explorar Libros — BooksMentor')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900">🔍 Explorar Biblioteca</h1>
        <p class="text-xs text-slate-500 mt-1">Elige entre libros clásicos y contemporáneos para enriquecer tu mente cada día.</p>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm">
        <form action="{{ route('dashboard.explorar') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-6">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por título o autor..." class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
            </div>
            <div class="sm:col-span-4">
                <select name="tag" class="w-full px-3.5 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    <option value="">Todas las temáticas</option>
                    @foreach($tags as $t)
                        <option value="{{ $t->slug }}" {{ request('tag') == $t->slug ? 'selected' : '' }}>{{ $t->icono }} {{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white text-xs font-bold rounded-xl">
                    Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Books Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($libros as $lib)
            <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-sm flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center gap-1.5 mb-2">
                        @foreach($lib->tags as $t)
                            <span class="px-2 py-0.5 rounded bg-brand-50 text-brand-700 text-[10px] font-semibold">{{ $t->icono }} {{ $t->nombre }}</span>
                        @endforeach
                    </div>

                    <h3 class="text-lg font-bold text-slate-900 leading-snug">{{ $lib->titulo }}</h3>
                    <p class="text-xs text-slate-500 mb-2">por {{ $lib->autor }}</p>
                    <p class="text-xs text-slate-600 line-clamp-3 leading-relaxed mb-4">{{ $lib->descripcion }}</p>
                </div>

                <div class="pt-3 border-t border-slate-100">
                    @if(in_array($lib->id, $misLibroIds))
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-600">✓ Ya estás suscrito</span>
                            <a href="{{ route('dashboard.leer', $lib->id) }}" class="px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 text-xs font-bold rounded-lg">
                                Leer
                            </a>
                        </div>
                    @else
                        <!-- Subscribe Form -->
                        <form action="{{ route('dashboard.suscribir') }}" method="POST" class="space-y-2">
                            @csrf
                            <input type="hidden" name="libro_id" value="{{ $lib->id }}">
                            <div class="flex flex-wrap gap-1 mb-2">
                                @foreach($idiomas as $lang)
                                    <label class="inline-flex items-center gap-1 text-[11px] text-slate-600">
                                        <input type="checkbox" name="idiomas[]" value="{{ $lang->id }}" {{ $loop->first ? 'checked' : '' }} class="rounded text-brand-600">
                                        <span>{{ $lang->codigo }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <button type="submit" class="w-full py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                                + Suscribirme a este libro
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">
        {{ $libros->links() }}
    </div>
</div>
@endsection