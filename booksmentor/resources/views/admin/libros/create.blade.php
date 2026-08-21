@extends('layouts.admin')

@section('title', 'Nuevo Libro con IA — Admin')
@section('breadcrumb', 'Crear Libro')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div>
        <h1 class="text-2xl font-black text-slate-900">✨ Crear y Procesar Libro con IA</h1>
        <p class="text-xs text-slate-500 mt-1">El LLM analizará el texto, extraerá lecciones clave y generará traducciones automáticas.</p>
    </div>

    <form action="{{ route('admin.libros.store') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Título del Libro *</label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Ej: Las 48 Leyes del Poder">
            </div>

            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Autor *</label>
                <input type="text" name="autor" value="{{ old('autor') }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Ej: Robert Greene">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Idioma del Texto Original</label>
                <select name="idioma_original_id" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    @foreach($idiomas as $lang)
                        <option value="{{ $lang->id }}" {{ $lang->codigo == 'es' ? 'selected' : '' }}>{{ $lang->nombre }} ({{ strtoupper($lang->codigo) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Cantidad de Lecciones a Extraer</label>
                <select name="cantidad_ensenanzas" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    <option value="5">5 lecciones</option>
                    <option value="10" selected>10 lecciones (Recomendado)</option>
                    <option value="15">15 lecciones</option>
                    <option value="20">20 lecciones</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-2">Idiomas a los que Traducir Automáticamente con LLM *</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                @foreach($idiomas as $lang)
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 cursor-pointer has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50">
                        <input type="checkbox" name="target_idiomas[]" value="{{ $lang->id }}" {{ in_array($lang->codigo, ['es', 'en', 'pt']) ? 'checked' : '' }} class="rounded text-brand-600">
                        <span>{{ $lang->nombre }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Etiquetas</label>
            <div class="flex flex-wrap gap-2 text-xs">
                @foreach($tags as $t)
                    <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 cursor-pointer hover:bg-slate-50 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50">
                        <input type="checkbox" name="tags[]" value="{{ $t->id }}" class="rounded text-brand-600">
                        <span>{{ $t->icono }} {{ $t->nombre }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Contenido / Notas / Texto Completo del Libro *</label>
            <textarea name="contenido" rows="8" required class="w-full p-4 text-xs font-mono border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-500" placeholder="Pega el contenido del libro aquí..."></textarea>
        </div>

        <button type="submit" class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-md transition-colors">
            ✨ Extraer Lecciones, Traducir con IA y Guardar Libro
        </button>
    </form>

</div>
@endsection