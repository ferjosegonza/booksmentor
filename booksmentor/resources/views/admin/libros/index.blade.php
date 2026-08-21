@extends('layouts.admin')

@section('title', 'Gestión de Libros — BooksMentor Admin')
@section('breadcrumb', 'Catálogo de Libros')

@section('content')
<div class="space-y-6">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Catálogo de Libros</h1>
            <p class="text-xs text-slate-500 mt-1">Crea, edita, traduce y gestiona los libros y enseñanzas del sistema.</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.libros.create') }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                + Nuevo Libro con IA
            </a>
            <a href="{{ route('admin.libros.bulk') }}" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
                📦 Carga Masiva IA
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
        <form action="{{ route('admin.libros.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-12 gap-3 text-xs">
            <div class="sm:col-span-6">
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por título o autor..." class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
            </div>
            <div class="sm:col-span-4">
                <select name="tag" class="w-full px-3 py-2 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    <option value="">Todas las etiquetas</option>
                    @foreach($tags as $t)
                        <option value="{{ $t->id }}" {{ request('tag') == $t->id ? 'selected' : '' }}>{{ $t->icono }} {{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full py-2 bg-slate-800 hover:bg-slate-900 text-white font-bold rounded-xl">Filtrar</button>
            </div>
        </form>
    </div>

    <!-- Books Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-100">
                    <tr>
                        <th class="py-3 px-4">Libro</th>
                        <th class="py-3 px-4">Idioma Original</th>
                        <th class="py-3 px-4">Lecciones</th>
                        <th class="py-3 px-4">Tags</th>
                        <th class="py-3 px-4">Estado</th>
                        <th class="py-3 px-4 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($libros as $lib)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3.5 px-4">
                                <a href="{{ route('admin.libros.show', $lib->id) }}" class="font-bold text-slate-900 hover:text-brand-600 text-sm block">
                                    {{ $lib->titulo }}
                                </a>
                                <span class="text-[11px] text-slate-400">por {{ $lib->autor }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-semibold text-[11px]">
                                    🌐 {{ $lib->idiomaOriginal ? $lib->idiomaOriginal->nombre : 'Español' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 font-bold text-slate-700">
                                📚 {{ $lib->cantidad_ensenanzas }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex flex-wrap gap-1">
                                    @foreach($lib->tags as $tag)
                                        <span class="px-1.5 py-0.5 rounded bg-brand-50 text-brand-700 text-[10px] font-semibold">{{ $tag->nombre }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <form action="{{ route('admin.libros.toggleActivo', $lib->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold cursor-pointer {{ $lib->activo ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                                        {{ $lib->activo ? 'Activo' : 'Inactivo' }}
                                    </button>
                                </form>
                            </td>
                            <td class="py-3.5 px-4 text-right space-x-1.5">
                                <a href="{{ route('admin.libros.show', $lib->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-brand-50 hover:text-brand-600 rounded-lg text-slate-700 font-semibold text-[11px]">Ver</a>
                                <a href="{{ route('admin.libros.edit', $lib->id) }}" class="px-2.5 py-1 bg-slate-100 hover:bg-slate-200 rounded-lg text-slate-700 font-semibold text-[11px]">Editar</a>
                                <form action="{{ route('admin.libros.destroy', $lib->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar este libro permanentemente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg font-semibold text-[11px]">✕</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $libros->links() }}
        </div>
    </div>

</div>
@endsection