@extends('layouts.admin')

@section('title', 'Enseñanzas — Admin')
@section('breadcrumb', 'Enseñanzas / Lecciones')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">Enseñanzas y Lecciones</h1>
            <p class="text-xs text-slate-500 mt-1">Explora todas las lecciones extraídas de los libros.</p>
        </div>
        <a href="{{ route('admin.ensenanzas.create') }}" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl">
            + Nueva Lección
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-100">
                <tr>
                    <th class="py-3 px-4">Libro</th>
                    <th class="py-3 px-4">Orden</th>
                    <th class="py-3 px-4">Tema</th>
                    <th class="py-3 px-4">Texto</th>
                    <th class="py-3 px-4">Traducciones</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($ensenanzas as $ens)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $ens->libro ? $ens->libro->titulo : '-' }}</td>
                        <td class="py-3.5 px-4 font-mono font-bold">#{{ $ens->orden }}</td>
                        <td class="py-3.5 px-4 font-semibold text-brand-700">{{ $ens->tema }}</td>
                        <td class="py-3.5 px-4 max-w-xs truncate">{{ $ens->texto_original }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-700 rounded font-bold text-[10px]">
                                {{ $ens->traducciones->count() }} idiomas
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1">
                            <a href="{{ route('admin.ensenanzas.edit', $ens->id) }}" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 rounded text-slate-700 text-[11px] font-semibold">Editar</a>
                            <form action="{{ route('admin.ensenanzas.destroy', $ens->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-rose-50 text-rose-700 rounded text-[11px] font-semibold">✕</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">{{ $ensenanzas->links() }}</div>
    </div>
</div>
@endsection