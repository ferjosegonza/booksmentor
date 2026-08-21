@extends('layouts.app')

@section('title', 'Mi Panel — BooksMentor')

@section('content')
<div class="space-y-8">
    
    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900">¡Hola, {{ $usuario->nombre ?: 'Lector' }}! 👋</h1>
            <p class="text-sm text-slate-500 mt-1">
                Plan: <strong class="text-brand-600">{{ $usuario->plan ? $usuario->plan->nombre : 'Gratuito' }}</strong> · Frecuencia: <strong>{{ $usuario->frecuencia ? $usuario->frecuencia->nombre : 'Diaria' }}</strong> · Hora preferida: <strong>{{ substr($usuario->hora_envio ?? '08:00', 0, 5) }}</strong>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
            <a href="{{ route('dashboard.libros.crear') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all hover:scale-105">
                <span>✨ Cargar Libro con IA</span>
            </a>
            <a href="{{ route('dashboard.explorar') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs rounded-xl transition-colors">
                <span>🔍 Explorar Catálogo</span>
            </a>
        </div>
    </div>

    <!-- Quick Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-xl font-bold">
                📚
            </div>
            <div>
                <span class="text-xs font-bold uppercase text-slate-400">Libros Activos</span>
                <h3 class="text-2xl font-black text-slate-900">{{ $suscripcionesActivas->count() }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                🏆
            </div>
            <div>
                <span class="text-xs font-bold uppercase text-slate-400">Libros Completados</span>
                <h3 class="text-2xl font-black text-slate-900">{{ $suscripcionesCompletadas->count() }}</h3>
            </div>
        </div>

        <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl font-bold">
                ⏰
            </div>
            <div>
                <span class="text-xs font-bold uppercase text-slate-400">Próximo Envío</span>
                <h3 class="text-sm font-bold text-slate-900">
                    {{ $proximoEnvio && $proximoEnvio->fecha_proximo_envio ? $proximoEnvio->fecha_proximo_envio->diffForHumans() : 'No programado' }}
                </h3>
            </div>
        </div>
    </div>

    <!-- Active Subscriptions Showcase -->
    <div>
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold text-slate-900">Mis Libros en Progreso</h2>
            <a href="{{ route('dashboard.suscripciones') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">Ver todas las suscripciones →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($suscripcionesActivas as $sub)
                <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between space-y-4">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-bold px-2 py-0.5 rounded bg-brand-50 text-brand-700">
                                Lección {{ $sub->ultima_ensenanza_enviada }}/{{ $sub->libro->cantidad_ensenanzas }}
                            </span>
                            <span class="text-xs font-bold text-emerald-600">
                                {{ $sub->porcentaje_avance }}%
                            </span>
                        </div>

                        <!-- Progress Bar -->
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden mb-3">
                            <div class="bg-brand-600 h-2 rounded-full transition-all" style="width: {{ $sub->porcentaje_avance }}%"></div>
                        </div>

                        <h3 class="font-bold text-slate-900 text-lg leading-snug mb-1">{{ $sub->libro->titulo }}</h3>
                        <p class="text-xs text-slate-500 mb-3">por {{ $sub->libro->autor }}</p>

                        <!-- Languages for this book -->
                        <div class="flex flex-wrap gap-1 mb-2">
                            @foreach($sub->idiomas as $lang)
                                <span class="text-[10px] font-bold uppercase px-1.5 py-0.5 bg-slate-100 text-slate-600 rounded">
                                    {{ $lang->codigo }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <a href="{{ route('dashboard.leer', [$sub->libro->id, max(1, $sub->ultima_ensenanza_enviada)]) }}" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                            📖 Leer ahora
                        </a>

                        <form action="{{ route('dashboard.suscripciones.enviarPrueba', $sub->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2.5 py-1 text-[11px] font-bold bg-slate-100 hover:bg-brand-50 hover:text-brand-600 text-slate-700 rounded-lg transition-colors" title="Enviar enseñanza ahora por email">
                                ✉️ Enviar a mi email
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-10 bg-white rounded-2xl border border-slate-200">
                    <p class="text-slate-500 text-sm mb-4">No tienes ningún libro activo en este momento.</p>
                    <a href="{{ route('dashboard.explorar') }}" class="px-4 py-2 bg-brand-600 text-white font-bold text-xs rounded-xl shadow-sm hover:bg-brand-700">
                        Explorar Libros Disponibles
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Recent History of Sent Teachings -->
    @if($historialReciente->isNotEmpty())
        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
            <h2 class="text-lg font-bold text-slate-900 mb-4">Historial Reciente de Lecciones Enviadas</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-slate-700 font-bold uppercase text-[10px]">
                        <tr>
                            <th class="py-2.5 px-3 rounded-l-lg">Libro</th>
                            <th class="py-2.5 px-3">Lección</th>
                            <th class="py-2.5 px-3">Idioma</th>
                            <th class="py-2.5 px-3">Estado</th>
                            <th class="py-2.5 px-3 rounded-r-lg">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($historialReciente as $hist)
                            <tr>
                                <td class="py-2.5 px-3 font-semibold text-slate-900">{{ $hist->ensenanza && $hist->ensenanza->libro ? $hist->ensenanza->libro->titulo : 'Libro' }}</td>
                                <td class="py-2.5 px-3">{{ $hist->ensenanza ? $hist->ensenanza->tema : '-' }} (#{{ $hist->ensenanza ? $hist->ensenanza->orden : '-' }})</td>
                                <td class="py-2.5 px-3"><span class="px-1.5 py-0.5 bg-slate-100 rounded text-[10px] font-bold uppercase">{{ $hist->idioma ? $hist->idioma->codigo : '-' }}</span></td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $hist->estado_id == 2 ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $hist->estado ? $hist->estado->nombre : 'Enviado' }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-slate-400">{{ $hist->fecha_envio ? $hist->fecha_envio->format('d/m/Y H:i') : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

</div>
@endsection