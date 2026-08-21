@extends('layouts.admin')

@section('title', 'Catálogos Maestros — Admin')
@section('breadcrumb', 'Catálogos')

@section('content')
<div class="space-y-8">
    <div>
        <h1 class="text-2xl font-black text-slate-900">🏷️ Tablas de Catálogo (Maestros)</h1>
        <p class="text-xs text-slate-500 mt-1">Configuración centralizada de frecuencias, planes, idiomas, etiquetas y tipos de sugerencia.</p>
    </div>

    <!-- Idiomas & Tags Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Idiomas -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900">Idiomas Disponibles ({{ $idiomas->count() }})</h2>
            </div>

            <form action="{{ route('admin.catalogos.storeIdioma') }}" method="POST" class="flex gap-2 text-xs">
                @csrf
                <input type="text" name="nombre" placeholder="Nombre (Ej: Japonés)" required class="px-3 py-2 border border-slate-200 rounded-xl flex-grow">
                <input type="text" name="codigo" placeholder="Código (ja)" required class="w-24 px-3 py-2 border border-slate-200 rounded-xl">
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white font-bold rounded-xl">+ Añadir</button>
            </form>

            <div class="divide-y divide-slate-100 text-xs">
                @foreach($idiomas as $lang)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900">{{ $lang->nombre }}</span>
                            <span class="text-slate-400 font-mono text-[11px] uppercase">({{ $lang->codigo }})</span>
                        </div>
                        <form action="{{ route('admin.catalogos.toggleIdioma', $lang->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $lang->activo ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $lang->activo ? 'Activo' : 'Inactivo' }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Tags -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-4">
            <div class="flex items-center justify-between">
                <h2 class="text-base font-bold text-slate-900">Etiquetas / Tags ({{ $tags->count() }})</h2>
            </div>

            <form action="{{ route('admin.catalogos.storeTag') }}" method="POST" class="flex gap-2 text-xs">
                @csrf
                <input type="text" name="icono" placeholder="Emoji 🎯" class="w-20 px-3 py-2 border border-slate-200 rounded-xl">
                <input type="text" name="nombre" placeholder="Nombre" required class="px-3 py-2 border border-slate-200 rounded-xl flex-grow">
                <input type="text" name="slug" placeholder="slug" required class="w-28 px-3 py-2 border border-slate-200 rounded-xl">
                <button type="submit" class="px-4 py-2 bg-brand-600 text-white font-bold rounded-xl">+</button>
            </form>

            <div class="flex flex-wrap gap-2 pt-2">
                @foreach($tags as $tag)
                    <span class="px-3 py-1 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 flex items-center gap-1.5">
                        <span>{{ $tag->icono }}</span>
                        <span>{{ $tag->nombre }}</span>
                    </span>
                @endforeach
            </div>
        </div>

    </div>

    <!-- Planes & Frecuencias Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- Planes -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-3 text-xs">
            <h2 class="text-base font-bold text-slate-900">Planes de Suscripción</h2>
            <div class="divide-y divide-slate-100">
                @foreach($planes as $p)
                    <div class="py-2.5 flex items-center justify-between">
                        <div>
                            <strong class="text-slate-900 text-sm block">{{ $p->nombre }}</strong>
                            <span class="text-slate-500">{{ $p->max_libros >= 999 ? 'Ilimitados' : $p->max_libros }} libros · {{ $p->max_idiomas }} idiomas · {{ $p->permite_audio ? 'Con Audio' : 'Sin Audio' }}</span>
                        </div>
                        <span class="font-black text-sm text-slate-900">${{ $p->precio_mensual }}/mes</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Frecuencias -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200 shadow-xs space-y-3 text-xs">
            <h2 class="text-base font-bold text-slate-900">Frecuencias de Envío</h2>
            <div class="divide-y divide-slate-100">
                @foreach($frecuencias as $f)
                    <div class="py-2.5 flex items-center justify-between">
                        <strong class="text-slate-900 text-sm">{{ $f->nombre }}</strong>
                        <span class="text-slate-500 font-mono">Cada {{ $f->dias_entre_envios }} día(s)</span>
                    </div>
                @endforeach
            </div>
        </div>

    </div>

</div>
@endsection