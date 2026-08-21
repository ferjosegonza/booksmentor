@extends('layouts.admin')

@section('title', 'Caché de Traducciones — Admin')
@section('breadcrumb', 'Traducciones')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900">🌐 Caché de Traducciones</h1>
        <p class="text-xs text-slate-500 mt-1">Todas las traducciones generadas y reutilizadas para envíos de email y lectura.</p>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-100">
                <tr>
                    <th class="py-3 px-4">Libro / Lección</th>
                    <th class="py-3 px-4">Idioma Destino</th>
                    <th class="py-3 px-4">Texto Traducido</th>
                    <th class="py-3 px-4">Reuso</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($traducciones as $trad)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3.5 px-4 font-bold text-slate-900">
                            {{ $trad->ensenanza && $trad->ensenanza->libro ? $trad->ensenanza->libro->titulo : '-' }}
                            <span class="text-xs text-slate-400 block">Lección #{{ $trad->ensenanza ? $trad->ensenanza->orden : '-' }}</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded bg-brand-50 text-brand-700 font-bold uppercase text-[11px]">
                                {{ $trad->idioma ? $trad->idioma->nombre : '-' }} ({{ $trad->idioma ? $trad->idioma->codigo : '-' }})
                            </span>
                        </td>
                        <td class="py-3.5 px-4 max-w-sm truncate text-slate-700">{{ $trad->texto_traducido }}</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-500">{{ $trad->veces_usado }} veces</td>
                        <td class="py-3.5 px-4 text-right space-x-1">
                            <form action="{{ route('admin.traducciones.regenerar', $trad->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2 py-1 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded text-[11px] font-bold" title="Re-traducir con LLM">
                                    🔄 IA
                                </button>
                            </form>
                            <a href="{{ route('admin.traducciones.edit', $trad->id) }}" class="px-2 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded text-[11px] font-semibold">Editar</a>
                            <form action="{{ route('admin.traducciones.destroy', $trad->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar del caché?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-rose-50 text-rose-700 rounded text-[11px]">✕</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">{{ $traducciones->links() }}</div>
    </div>
</div>
@endsection