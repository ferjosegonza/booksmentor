@extends('layouts.app')

@section('title', 'Carga Masiva de Libros con IA — BooksMentor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div>
        <h1 class="text-2xl font-black text-slate-900">📦 Carga Masiva de Libros con IA</h1>
        <p class="text-xs text-slate-500 mt-1">Importa una lista completa de libros en formato JSON o línea por línea. El LLM los procesará y traducirá a los idiomas seleccionados.</p>
    </div>

    <form action="{{ route('dashboard.libros.guardarBulk') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-sm space-y-6">
        @csrf

        <div>
            <label class="block text-xs font-bold uppercase text-slate-700 mb-2">Idiomas de Destino para Traducir con LLM *</label>
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
            <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-bold uppercase text-slate-700">Lista de Libros (JSON o Separado por |) *</label>
                <button type="button" onclick="fillSampleBulk()" class="text-xs text-brand-600 font-bold hover:underline">Insertar Ejemplo</button>
            </div>
            <textarea id="bulk_data" name="bulk_data" rows="10" required class="w-full p-4 text-xs font-mono border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-500" placeholder='[
  {
    "titulo": "Meditaciones",
    "autor": "Marco Aurelio",
    "descripcion": "Reflexiones filosóficas de un emperador estoico.",
    "contenido": "La felicidad de tu vida depende de la calidad de tus pensamientos..."
  }
]'></textarea>
        </div>

        <button type="submit" class="w-full py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-2xl shadow-lg shadow-brand-500/25 transition-all">
            🚀 Procesar Todos los Libros con IA en Lote
        </button>
    </form>

</div>

<script>
function fillSampleBulk() {
    const sample = [
        {
            "titulo": "Meditaciones",
            "autor": "Marco Aurelio",
            "descripcion": "El diario íntimo y guía de vida del emperador estoico.",
            "contenido": "Tienes poder sobre tu mente, no sobre los acontecimientos. Date cuenta de esto y encontrarás la fuerza."
        },
        {
            "titulo": "El Arte de la Guerra",
            "autor": "Sun Tzu",
            "descripcion": "Estrategia milenaria para superar cualquier conflicto.",
            "contenido": "El supremo arte de la guerra es someter al enemigo sin luchar. Conócete a ti mismo y conoce a tu adversario."
        }
    ];
    document.getElementById('bulk_data').value = JSON.stringify(sample, null, 2);
}
</script>
@endsection