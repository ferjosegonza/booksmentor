@extends('layouts.admin')

@section('title', 'Configuración de LLM & Sistema — BooksMentor')
@section('breadcrumb', 'Configuración & LLM')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    
    <div>
        <h1 class="text-2xl font-black text-slate-900">⚙️ Configuración del Motor LLM e Idiomas</h1>
        <p class="text-xs text-slate-500 mt-1">Configura las credenciales de DeepSeek, Groq, OpenAI o utiliza el motor gratuito integrado.</p>
    </div>

    <!-- Live Test Connection Box -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        <h2 class="text-base font-bold text-slate-900">1. Probar Conexión con LLM / Motor de Traducción</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Proveedor a Probar</label>
                <select id="test_provider" class="w-full px-3.5 py-2.5 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
                    <option value="deepseek" {{ $currentProvider == 'deepseek' ? 'selected' : '' }}>DeepSeek API</option>
                    <option value="groq" {{ $currentProvider == 'groq' ? 'selected' : '' }}>Groq Cloud API</option>
                    <option value="openai" {{ $currentProvider == 'openai' ? 'selected' : '' }}>OpenAI API</option>
                    <option value="ollama" {{ $currentProvider == 'ollama' ? 'selected' : '' }}>Ollama Local (11434)</option>
                    <option value="free_fallback">Motor Gratuito Integrado</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">API Key de Prueba (Opcional, si no se usa la de .env)</label>
                <input type="text" id="test_api_key" placeholder="sk-..." class="w-full px-3.5 py-2.5 text-xs font-mono border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">
            </div>
        </div>

        <button type="button" onclick="runLLMTest()" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-sm transition-colors">
            ⚡ Ejecutar Test de Conexión
        </button>

        <div id="llm-test-result" class="hidden p-4 rounded-2xl text-xs font-mono"></div>
    </div>

    <!-- Live Translation Tester -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-6">
        <h2 class="text-base font-bold text-slate-900">2. Probador Interactivo de Traducción</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Texto de Entrada</label>
                <textarea id="translate_input" rows="3" class="w-full p-3 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-brand-500">No cuentes los días, haz que los días cuenten.</textarea>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase text-slate-700 mb-1">Resultado Traducido</label>
                <textarea id="translate_output" rows="3" readonly class="w-full p-3 text-xs bg-slate-50 border border-slate-200 rounded-xl font-medium text-brand-900"></textarea>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="flex items-center gap-2 text-xs">
                <span class="font-bold">De:</span>
                <select id="trans_source" class="px-2 py-1 border border-slate-200 rounded-lg text-xs">
                    <option value="es">Español (es)</option>
                    <option value="en">English (en)</option>
                    <option value="pt">Português (pt)</option>
                </select>
            </div>

            <div class="flex items-center gap-2 text-xs">
                <span class="font-bold">A:</span>
                <select id="trans_target" class="px-2 py-1 border border-slate-200 rounded-lg text-xs">
                    <option value="en">English (en)</option>
                    <option value="zh">中文 (zh)</option>
                    <option value="pt">Português (pt)</option>
                    <option value="fr">Français (fr)</option>
                    <option value="it">Italiano (it)</option>
                </select>
            </div>

            <button type="button" onclick="runTranslateTest()" class="px-5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl shadow-sm">
                Traducir Ahora
            </button>
        </div>
    </div>

    <!-- Active Environment Variables Info -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200 shadow-xs space-y-4">
        <h2 class="text-base font-bold text-slate-900">3. Variables de Entorno LLM (.env)</h2>
        <div class="space-y-2 text-xs font-mono bg-slate-50 p-4 rounded-2xl border border-slate-200 text-slate-700">
            <p><strong>LLM_DEFAULT_PROVIDER:</strong> {{ $currentProvider }}</p>
            <p><strong>DEEPSEEK_API_KEY:</strong> {{ $deepseekKey ? substr($deepseekKey, 0, 6) . '...' . substr($deepseekKey, -4) : '(No configurada - usando motor gratuito)' }}</p>
            <p><strong>DEEPSEEK_BASE_URL:</strong> {{ $deepseekUrl }}</p>
            <p><strong>GROQ_API_KEY:</strong> {{ $groqKey ? substr($groqKey, 0, 6) . '...' : '(No configurada)' }}</p>
            <p><strong>OPENAI_API_KEY:</strong> {{ $openaiKey ? substr($openaiKey, 0, 6) . '...' : '(No configurada)' }}</p>
        </div>
    </div>

</div>

@push('scripts')
<script>
function runLLMTest() {
    const provider = document.getElementById('test_provider').value;
    const apiKey = document.getElementById('test_api_key').value;
    const resultBox = document.getElementById('llm-test-result');

    resultBox.className = 'p-4 rounded-2xl text-xs font-mono bg-slate-100 text-slate-700';
    resultBox.innerText = 'Probando conexión con ' + provider + '...';
    resultBox.classList.remove('hidden');

    fetch("{{ route('admin.testLLM') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ provider: provider, api_key: apiKey })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            resultBox.className = 'p-4 rounded-2xl text-xs font-mono bg-emerald-50 border border-emerald-200 text-emerald-900';
            resultBox.innerText = '✓ ' + data.message;
        } else {
            resultBox.className = 'p-4 rounded-2xl text-xs font-mono bg-amber-50 border border-amber-200 text-amber-900';
            resultBox.innerText = 'ℹ ' + data.message;
        }
    })
    .catch(err => {
        resultBox.className = 'p-4 rounded-2xl text-xs font-mono bg-rose-50 border border-rose-200 text-rose-900';
        resultBox.innerText = 'Error en la petición: ' + err.message;
    });
}

function runTranslateTest() {
    const text = document.getElementById('translate_input').value;
    const src = document.getElementById('trans_source').value;
    const tgt = document.getElementById('trans_target').value;
    const output = document.getElementById('translate_output');

    output.value = 'Traduciendo con IA...';

    fetch("{{ route('admin.testTranslate') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ texto: text, source: src, target: tgt })
    })
    .then(r => r.json())
    .then(data => {
        output.value = data.translated;
    })
    .catch(err => {
        output.value = 'Error: ' + err.message;
    });
}
</script>
@endpush
@endsection