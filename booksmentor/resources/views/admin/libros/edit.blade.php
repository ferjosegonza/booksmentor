@extends('layouts.admin')

@section('title', 'Editar: ' . $libro->titulo . ' — Admin')
@section('breadcrumb', 'Editar Libro')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <h1 class="text-2xl font-black text-slate-900">Editar Libro</h1>

    <form action="{{ route('admin.libros.update', $libro->id) }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4 text-xs">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Título *</label>
                <input type="text" name="titulo" value="{{ old('titulo', $libro->titulo) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
            </div>

            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Autor *</label>
                <input type="text" name="autor" value="{{ old('autor', $libro->autor) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
            </div>
        </div>

        <div>
            <label class="block font-bold uppercase text-slate-700 mb-1">Idioma Original</label>
            <select name="idioma_original_id" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
                @foreach($idiomas as $lang)
                    <option value="{{ $lang->id }}" {{ $libro->idioma_original_id == $lang->id ? 'selected' : '' }}>{{ $lang->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block font-bold uppercase text-slate-700 mb-1">Descripción</label>
            <textarea name="descripcion" rows="3" class="w-full p-3 border border-slate-200 rounded-xl">{{ old('descripcion', $libro->descripcion) }}</textarea>
        </div>

        <div class="flex items-center gap-2">
            <input type="checkbox" name="activo" value="1" {{ $libro->activo ? 'checked' : '' }} class="rounded text-brand-600">
            <span class="font-bold text-slate-700">Libro Activo y Disponible en el Catálogo</span>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl">
            Guardar Cambios
        </button>
    </form>
</div>
@endsection