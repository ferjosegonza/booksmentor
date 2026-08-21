@extends('layouts.admin')

@section('title', 'Añadir Lección — Admin')
@section('breadcrumb', 'Añadir Lección')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <h1 class="text-2xl font-black text-slate-900">Añadir Lección a un Libro</h1>

    <form action="{{ route('admin.ensenanzas.store') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4 text-xs">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Libro *</label>
                <select name="libro_id" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
                    @foreach($libros as $lib)
                        <option value="{{ $lib->id }}" {{ $lib->id == $selectedLibroId ? 'selected' : '' }}>{{ $lib->titulo }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Número de Orden *</label>
                <input type="number" name="orden" value="{{ $siguienteOrden }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
            </div>
        </div>

        <div>
            <label class="block font-bold uppercase text-slate-700 mb-1">Tema / Título de la Lección *</label>
            <input type="text" name="tema" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl" placeholder="Ej: La perseverancia y el propósito">
        </div>

        <div>
            <label class="block font-bold uppercase text-slate-700 mb-1">Texto de la Lección (Idioma Original del Libro) *</label>
            <textarea name="texto_original" rows="5" required class="w-full p-3 border border-slate-200 rounded-xl" placeholder="Escribe el texto de la enseñanza..."></textarea>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="auto_traducir" value="1" checked class="rounded text-brand-600">
            <span class="font-bold text-slate-700">Traducir automáticamente a todos los idiomas del catálogo con LLM</span>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl">
            Guardar Lección
        </button>
    </form>
</div>
@endsection