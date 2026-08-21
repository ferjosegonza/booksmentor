@extends('layouts.public')

@section('title', 'Planes y Precios — BooksMentor')

@section('content')
<div class="bg-slate-50 py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <h1 class="text-4xl font-black text-slate-900 tracking-tight">Planes Simples y Transparentes</h1>
            <p class="text-lg text-slate-600 mt-3">Empieza gratis y mejora tu plan cuando quieras leer más libros o recibir lecciones en más idiomas.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($planes as $plan)
                <div class="rounded-3xl border {{ $plan->id == 3 ? 'border-brand-500 shadow-xl ring-2 ring-brand-500/20 bg-brand-50/20' : 'border-slate-200 shadow-sm bg-white' }} p-8 flex flex-col justify-between">
                    <div>
                        @if($plan->id == 3)
                            <span class="px-3 py-1 bg-brand-600 text-white text-[11px] font-extrabold uppercase tracking-wider rounded-full self-start mb-3 inline-block">Más Popular</span>
                        @endif
                        <h3 class="text-2xl font-bold text-slate-900">{{ $plan->nombre }}</h3>
                        <div class="mt-4 mb-6">
                            <span class="text-4xl font-black text-slate-900">${{ number_format($plan->precio_mensual, 0) }}</span>
                            <span class="text-xs text-slate-500 font-medium">/ mes</span>
                        </div>

                        <ul class="space-y-3.5 text-sm text-slate-600 mb-8">
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Hasta <strong>{{ $plan->max_libros >= 999 ? 'Ilimitados' : $plan->max_libros }}</strong> libros activos</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Hasta <strong>{{ $plan->max_idiomas }}</strong> idioma(s) por libro</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="{{ $plan->permite_audio ? 'text-emerald-500' : 'text-slate-300' }} font-bold">{{ $plan->permite_audio ? '✓' : '—' }}</span>
                                <span class="{{ $plan->permite_audio ? 'text-slate-700 font-medium' : 'text-slate-400' }}">Audio Text-to-Speech</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Traducción instantánea con IA</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register', ['plan_id' => $plan->id]) }}" class="w-full py-3 px-4 text-center text-sm font-bold rounded-xl {{ $plan->id == 3 ? 'bg-brand-600 hover:bg-brand-700 text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200 text-slate-800' }} transition-colors">
                        Elegir {{ $plan->nombre }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection