@extends('layouts.admin')

@section('title', 'Editar Lección — Admin')
@section('breadcrumb', 'Editar Lección')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <h1 class="text-2xl font-black text-slate-900">Editar Lección #{{ $ensenanza->orden }} · {{ $ensenanza->libro->titulo }}</h1>

    <form action="{{ route('admin.ensenanzas.update', $ensenanza->id) }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4 text-xs">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Orden</label>
                <input type="number" name="orden" value="{{ old('orden', $ensenanza->orden) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
            </div>

            <div>
                <label class="block font-bold uppercase text-slate-700 mb-1">Tema *</label>
                <input type="text" name="tema" value="{{ old('tema', $ensenanza->tema) }}" required class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl">
            </div>
        </div>

        <div>
            <label class="block font-bold uppercase text-slate-700 mb-1">Texto Original *</label>
            <textarea name="texto_original" rows="5" required class="w-full p-3 border border-slate-200 rounded-xl">{{ old('texto_original', $ensenanza->texto_original) }}</textarea>
        </div>

        <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl">
            Guardar Cambios
        </button>
    </form>
</div>
@endsection