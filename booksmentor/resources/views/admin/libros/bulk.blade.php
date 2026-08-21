@extends('layouts.admin')

@section('title', 'Carga Masiva de Libros — Admin')
@section('breadcrumb', 'Carga Masiva')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div>
        <h1 class="text-2xl font-black text-slate-900">📦 Carga Masiva de Libros con LLM</h1>
        <p class="text-xs text-slate-500 mt-1">Inserta una lista de libros en formato JSON o delimitada por barra vertical (|).</p>
    </div>

    <form action="{{ route('admin.libros.storeBulk') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-2">Idiomas a los que el LLM debe Traducir *</label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs">
                @foreach($idiomas as $lang)
                    <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 hover:bg-slate-50 has-[:checked]:border-brand-600 has-[:checked]:bg-brand-50">
                        <input type="checkbox" name="target_idiomas[]" value="{{ $lang->id }}" {{ in_array($lang->codigo, ['es', 'en', 'pt']) ? 'checked' : '' }} class="rounded text-brand-600">
                        <span>{{ $lang->nombre }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Datos Masivos *</label>
            <textarea name="bulk_data" rows="10" required class="w-full p-4 text-xs font-mono border border-slate-200 rounded-2xl" placeholder='[
  {
    "titulo": "Sapiens",
    "autor": "Yuval Noah Harari",
    "contenido": "Los humanos conquistaron el mundo gracias a su capacidad de cooperar en base a mitos comunes..."
  }
]'></textarea>
        </div>

        <button type="submit" class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-md">
            🚀 Procesar Todos los Libros con IA
        </button>
    </form>
</div>
@endsection