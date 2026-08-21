@extends('layouts.admin')

@section('title', 'Editar Traducción — Admin')
@section('breadcrumb', 'Editar Traducción')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <h1 class="text-2xl font-black text-slate-900">Editar Traducción ({{ $traduccion->idioma->nombre }})</h1>

    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 text-xs text-slate-700 space-y-1">
        <strong class="text-slate-900">Texto Original ({{ $traduccion->ensenanza->libro->idiomaOriginal->nombre }}):</strong>
        <p class="italic font-serif">{{ $traduccion->ensenanza->texto_original }}</p>
    </div>

    <form action="{{ route('admin.traducciones.update', $traduccion->id) }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4 text-xs">
        @csrf @method('PUT')

        <div>
            <label class="block font-bold uppercase text-slate-700 mb-1">Texto Traducido *</label>
            <textarea name="texto_traducido" rows="5" required class="w-full p-3 border border-slate-200 rounded-xl">{{ old('texto_traducido', $traduccion->texto_traducido) }}</textarea>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl">
            Guardar Traducción
        </button>
    </form>
</div>
@endsection