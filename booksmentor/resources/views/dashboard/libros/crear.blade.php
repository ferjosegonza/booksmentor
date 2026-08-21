@extends('layouts.app')

@section('title', 'Cargar Libro Propio con IA — BooksMentor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900">✨ Cargar Libro o Notas con IA</h1>
            <p class="text-xs text-slate-500 mt-1">Pega el texto, resumen o ideas de cualquier libro. El LLM extraerá las mejores lecciones y las traducirá a tus idiomas.</p>
        </div>
        <a href="{{ route('dashboard.libros.bulk') }}" class="px-3.5 py-2 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors">
            📦 ¿Prefieres Carga Masiva?
        </a>
    </div>

    <form action="{{ route('dashboard.libros.guardar') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Título del Libro *</label>
                <input type="text" name="titulo" value="{{ old('titulo') }}" required class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Ej: Pensar Rápido, Pensar Despacio">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Autor *</label>
                <input type="text" name="autor" value="{{ old('autor') }}" required class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500" placeholder="Ej: Daniel Kahneman">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Idioma del Texto Original *</label>
                <select name="idioma_original_id" class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    @foreach($idiomas as $lang)
                        <option value="{{ $lang->id }}" {{ $lang->codigo == 'es' ? 'selected' : '' }}>
                            {{ $lang->nombre }} ({{ strtoupper($lang->codigo) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Cantidad de Lecciones a Extraer con IA</label>
                <select name="cantidad_ensenanzas" class="w-full px-3.5 py-2.5 text-sm border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    <option value="5">5 lecciones diarias</option>
                    <option value="10" selected>10 lecciones diarias (Recomendado)</option>
                    <option value="15">15 lecciones diarias</option>
                    <option value="20">20 lecciones diarias</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-2">Idiomas a los que el LLM debe Traducir Automáticamente *</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                @foreach($idiomas as $lang)
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 text-xs font-medium cursor-pointer hover:bg-slate-50 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50/50">
                        <input type="checkbox" name="target_idiomas[]" value="{{ $lang->id }}" {{ in_array($lang->codigo, ['es', 'en']) ? 'checked' : '' }} class="rounded text-brand-600 focus:ring-brand-500">
                        <span>{{ $lang->nombre }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Temáticas / Etiquetas (opcional)</label>
            <div class="flex flex-wrap gap-2">
                @foreach($tags as $tag)
                    <label class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs cursor-pointer hover:bg-slate-50 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" class="rounded text-brand-600 focus:ring-brand-500">
                        <span>{{ $tag->icono }} {{ $tag->nombre }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Contenido / Texto / Resumen del Libro *</label>
            <p class="text-[11px] text-slate-400 mb-2">Pega capítulos, citas, notas o el texto completo. La IA analizará la esencia y creará lecciones claras.</p>
            <textarea name="contenido" rows="8" required class="w-full p-4 text-sm font-mono border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-500" placeholder="Pega aquí el contenido del libro, notas o ideas principales..."></textarea>
        </div>

        <button type="submit" class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-2xl shadow-lg shadow-brand-500/25 transition-all flex items-center justify-center gap-2">
            <span>✨ Procesar, Traducir con IA y Suscribirme</span>
        </button>
    </form>

</div>
@endsection