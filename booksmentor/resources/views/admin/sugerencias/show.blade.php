@extends('layouts.admin')

@section('title', 'Sugerencia #' . $sugerencia->id . ' — Admin')
@section('breadcrumb', 'Ver Sugerencia')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <span class="font-bold text-brand-600 block text-sm">{{ $sugerencia->tipo ? $sugerencia->tipo->nombre : 'Sugerencia' }}</span>
                <span class="text-slate-400">De: <strong>{{ $sugerencia->email }}</strong> · Fecha: {{ $sugerencia->fecha_envio ? $sugerencia->fecha_envio->format('d/m/Y H:i') : '-' }}</span>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $sugerencia->atendido ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                {{ $sugerencia->atendido ? 'Atendido' : 'Pendiente' }}
            </span>
        </div>

        @if($sugerencia->libro_sugerido)
            <div class="p-3 bg-brand-50 rounded-xl font-bold text-brand-900">
                📚 Libro Sugerido: {{ $sugerencia->libro_sugerido }}
            </div>
        @endif

        <div class="space-y-1">
            <strong class="text-slate-700 block">Mensaje del Usuario:</strong>
            <p class="p-4 bg-slate-50 rounded-2xl border border-slate-100 text-slate-800 text-sm leading-relaxed">{{ $sugerencia->mensaje }}</p>
        </div>

        <!-- Admin Reply Form -->
        <form action="{{ route('admin.sugerencias.responder', $sugerencia->id) }}" method="POST" class="pt-4 border-t border-slate-100 space-y-3">
            @csrf
            <label class="block font-bold uppercase text-slate-700">Respuesta del Administrador</label>
            <textarea name="respuesta_admin" rows="4" required class="w-full p-3 border border-slate-200 rounded-xl" placeholder="Escribe tu respuesta para el usuario...">{{ old('respuesta_admin', $sugerencia->respuesta_admin) }}</textarea>
            
            <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl shadow-sm">
                Guardar Respuesta y Marcar como Atendido
            </button>
        </form>
    </div>
</div>
@endsection