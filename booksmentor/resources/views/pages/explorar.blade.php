@extends('layouts.public')

@section('title', 'Explorar Catálogo de Libros — BooksMentor')

@section('content')
<div class="bg-slate-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="max-w-3xl mb-8">
            <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Catálogo de Libros</h1>
            <p class="text-slate-600 mt-2">Encuentra libros transformadores y suscríbete para recibir sus enseñanzas cada día.</p>
        </div>

        <!-- Filters & Search -->
        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-slate-200 mb-8">
            <form action="{{ route('explorar') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                <div class="sm:col-span-6">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Buscar por título o autor</label>
                    <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Ej: Hábitos, Frankl, Dinero..." class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500">
                </div>

                <div class="sm:col-span-3">
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Temática / Tag</label>
                    <select name="tag" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500">
                        <option value="">Todas las temáticas</option>
                        @foreach($tags as $t)
                            <option value="{{ $t->slug }}" {{ request('tag') == $t->slug ? 'selected' : '' }}>{{ $t->icono }} {{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-3 flex items-end">
                    <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white text-sm font-bold rounded-lg shadow-sm transition-colors">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>

        <!-- Books Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($libros as $libro)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            @foreach($libro->tags as $tag)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-brand-50 text-brand-700">
                                    {{ $tag->icono }} {{ $tag->nombre }}
                                </span>
                            @endforeach
                            <span class="text-xs font-medium text-slate-400 ml-auto">
                                🌐 {{ $libro->idiomaOriginal ? $libro->idiomaOriginal->nombre : 'Español' }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $libro->titulo }}</h3>
                        <p class="text-sm text-slate-500 font-medium mb-3">por {{ $libro->autor }}</p>
                        <p class="text-sm text-slate-600 line-clamp-3 leading-relaxed mb-4">
                            {{ $libro->descripcion ?: 'Resumen estructurado y lecciones diarias extraídas con IA.' }}
                        </p>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600">📚 {{ $libro->cantidad_ensenanzas }} Lecciones</span>
                        <a href="{{ route('libro.detalle', $libro->id) }}" class="px-3.5 py-1.5 text-xs font-bold text-brand-600 bg-white border border-brand-200 rounded-lg hover:bg-brand-50 transition-colors">
                            Ver Detalles →
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12 bg-white rounded-2xl border border-slate-200">
                    <p class="text-slate-500 text-sm">No se encontraron libros con los filtros seleccionados.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $libros->links() }}
        </div>
    </div>
</div>
@endsection