@extends('layouts.app')

@section('title', 'Sugerencias y Feedback — BooksMentor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div>
        <h1 class="text-2xl font-black text-slate-900">💡 Sugerencias y Reportes</h1>
        <p class="text-xs text-slate-500 mt-1">¿Quieres que agreguemos un libro nuevo o encontraste algo que mejorar? Cuéntanos aquí.</p>
    </div>

    <!-- Form -->
    <form action="{{ route('dashboard.sugerencias.guardar') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-4">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Tipo de Mensaje</label>
                <select name="tipo_id" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    @foreach($tipos as $t)
                        <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Libro Sugerido (opcional)</label>
                <input type="text" name="libro_sugerido" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Título y Autor del libro">
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Tu Mensaje o Comentario *</label>
            <textarea name="mensaje" rows="4" required class="w-full p-4 text-xs border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-500" placeholder="Escribe aquí tu sugerencia o feedback..."></textarea>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-md transition-colors">
            Enviar Sugerencia
        </button>
    </form>

    <!-- History -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm space-y-4">
        <h2 class="text-lg font-bold text-slate-900">Mis Mensajes Anteriores</h2>

        <div class="space-y-4">
            @forelse($sugerencias as $sug)
                <div class="p-4 rounded-2xl border border-slate-100 bg-slate-50 space-y-2">
                    <div class="flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-700">{{ $sug->tipo ? $sug->tipo->nombre : 'Sugerencia' }}</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sug->atendido ? 'bg-emerald-100 text-emerald-800' : ($sug->leido ? 'bg-blue-100 text-blue-800' : 'bg-slate-200 text-slate-700') }}">
                            {{ $sug->atendido ? '✓ Atendido' : ($sug->leido ? 'Leído' : 'Pendiente') }}
                        </span>
                    </div>

                    @if($sug->libro_sugerido)
                        <p class="text-xs font-semibold text-brand-700">Libro: {{ $sug->libro_sugerido }}</p>
                    @endif

                    <p class="text-xs text-slate-600 leading-relaxed">{{ $sug->mensaje }}</p>

                    @if($sug->respuesta_admin)
                        <div class="mt-3 p-3 bg-white rounded-xl border border-brand-100 text-xs text-brand-900">
                            <span class="font-bold block mb-1">Respuesta del Administrador:</span>
                            {{ $sug->respuesta_admin }}
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-6">No has enviado ninguna sugerencia todavía.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection