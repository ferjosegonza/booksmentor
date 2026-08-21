@extends('layouts.admin')

@section('title', 'Suscripciones y Envíos — Admin')
@section('breadcrumb', 'Suscripciones')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">📬 Suscripciones de Usuarios</h1>
            <p class="text-xs text-slate-500 mt-1">Supervisa y administra el avance de cada lector en sus libros.</p>
        </div>
        <form action="{{ route('admin.ejecutarCron') }}" method="POST">
            @csrf
            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm">
                ⚡ Despachar Lote Ahora
            </button>
        </form>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200 shadow-xs overflow-hidden">
        <table class="w-full text-left text-xs text-slate-600">
            <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px] border-b border-slate-100">
                <tr>
                    <th class="py-3 px-4">Usuario</th>
                    <th class="py-3 px-4">Libro</th>
                    <th class="py-3 px-4">Progreso</th>
                    <th class="py-3 px-4">Idiomas</th>
                    <th class="py-3 px-4">Estado</th>
                    <th class="py-3 px-4">Próximo Envío</th>
                    <th class="py-3 px-4 text-right">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($suscripciones as $sub)
                    <tr class="hover:bg-slate-50/60">
                        <td class="py-3.5 px-4 font-bold text-slate-900">{{ $sub->usuario ? $sub->usuario->email : '-' }}</td>
                        <td class="py-3.5 px-4 font-semibold text-slate-800">{{ $sub->libro ? $sub->libro->titulo : '-' }}</td>
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-brand-600">{{ $sub->ultima_ensenanza_enviada }}/{{ $sub->libro ? $sub->libro->cantidad_ensenanzas : 0 }}</span>
                            <span class="text-slate-400">({{ $sub->porcentaje_avance }}%)</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="flex flex-wrap gap-1">
                                @foreach($sub->idiomas as $l)
                                    <span class="px-1.5 py-0.5 rounded bg-slate-100 text-[10px] font-bold uppercase">{{ $l->codigo }}</span>
                                @endforeach
                            </div>
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $sub->estado_id == 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $sub->estado ? $sub->estado->nombre : '-' }}
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-slate-400">{{ $sub->fecha_proximo_envio ? $sub->fecha_proximo_envio->format('d/m/Y H:i') : '-' }}</td>
                        <td class="py-3.5 px-4 text-right space-x-1">
                            <form action="{{ route('admin.suscripciones.forzarEnvio', $sub->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="px-2 py-1 bg-brand-50 hover:bg-brand-100 text-brand-700 rounded text-[11px] font-bold" title="Enviar siguiente lección ya">
                                    ✉️ Enviar
                                </button>
                            </form>
                            <form action="{{ route('admin.suscripciones.destroy', $sub->id) }}" method="POST" class="inline" onsubmit="return confirm('¿Eliminar suscripción?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-rose-50 text-rose-700 rounded text-[11px]">✕</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-100">{{ $suscripciones->links() }}</div>
    </div>
</div>
@endsection