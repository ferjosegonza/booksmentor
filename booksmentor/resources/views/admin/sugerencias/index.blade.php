@extends('layouts.admin')

@section('title', 'Sugerencias de Usuarios — Admin')
@section('breadcrumb', 'Bandeja de Sugerencias')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900">💡 Sugerencias y Reportes de Usuarios</h1>
        <p class="text-xs text-slate-500 mt-1">Revisa propuestas de libros, reportes de errores y responde directamente a los usuarios.</p>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-100">
                <tr>
                    <th class="py-3 px-4">Tipo</th>
                    <th class="py-3 px-4">Usuario / Email</th>
                    <th class="py-3 px-4">Libro Sugerido</th>
                    <th class="py-3 px-4">Mensaje</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($sugerencias as $sug)
                    <tr class="hover:bg-slate-50/60 {{ !$sug->leido ? 'bg-amber-50/30 font-semibold' : '' }}">
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $sug->tipo ? $sug->tipo->nombre : '-' }}</td>
                        <td class="py-3.5 px-4">{{ $sug->email }}</td>
                        <td class="py-3.5 px-4 font-semibold text-brand-700">{{ $sug->libro_sugerido ?: '—' }}</td>
                        <td class="py-3.5 px-4 max-w-xs truncate">{{ $sug->mensaje }}</td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sug->atendido ? 'bg-emerald-100 text-emerald-800' : ($sug->leido ? 'bg-blue-100 text-blue-800' : 'bg-amber-100 text-amber-800') }}">
                                {{ $sug->atendido ? '✓ Atendido' : ($sug->leido ? 'Leído' : 'Nuevo') }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right space-x-1">
                            <a href="{{ route('admin.sugerencias.show', $sug->id) }}" class="px-2.5 py-1 bg-brand-600 text-white rounded text-[11px] font-bold hover:bg-brand-700">Ver / Responder</a>
                            <form action="{{ route('admin.sugerencias.destroy', $sug->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar mensaje?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-rose-50 text-rose-700 rounded text-[11px]">✕</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">{{ $sugerencias->links() }}</div>
    </div>
</div>
@endsection