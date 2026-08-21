@extends('layouts.app')

@section('title', 'Lector: ' . $libro->titulo . ' — BooksMentor')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Reader Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200 shadow-sm">
        <div>
            <a href="{{ route('dashboard.suscripciones') }}" class="text-xs font-bold text-brand-600 hover:underline mb-1 inline-block">← Volver a mis libros</a>
            <h1 class="text-2xl font-black text-slate-900">{{ $libro->titulo }}</h1>
            <p class="text-xs text-slate-500">por {{ $libro->autor }} · Idioma Original: <strong>{{ $libro->idiomaOriginal ? $libro->idiomaOriginal->nombre : 'Español' }}</strong></p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Audio Button -->
            <button id="reader-tts-btn" onclick="playReaderAudio()" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-sm transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                <span>Escuchar Audio</span>
            </button>
        </div>
    </div>

    @if($ensenanza)
        <div class="bg-white rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm space-y-6">
            
            <!-- Teaching Navigation Bar -->
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 text-xs">
                @if($orden > 1)
                    <a href="{{ route('dashboard.leer', [$libro->id, $orden - 1]) }}" class="font-bold text-brand-600 hover:underline">← Lección Anterior</a>
                @else
                    <span class="text-slate-300">← Lección Anterior</span>
                @endif

                <span class="font-bold text-slate-700 bg-slate-100 px-3 py-1 rounded-full">
                    Lección #{{ $ensenanza->orden }} de {{ $libro->cantidad_ensenanzas }}
                </span>

                @if($orden < $libro->cantidad_ensenanzas)
                    <a href="{{ route('dashboard.leer', [$libro->id, $orden + 1]) }}" class="font-bold text-brand-600 hover:underline">Siguiente Lección →</a>
                @else
                    <span class="text-slate-300">Siguiente Lección →</span>
                @endif
            </div>

            <!-- Theme Title -->
            <div>
                <span class="text-xs uppercase font-bold tracking-wider text-brand-600">Tema del Día</span>
                <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-1">{{ $ensenanza->tema }}</h2>
            </div>

            <!-- Multi-language Tabs -->
            <div class="flex flex-wrap gap-2 pt-2 border-b border-slate-100 pb-3" id="lang-tabs-bar">
                <button onclick="switchReaderLang('original', this)" class="reader-lang-tab px-3.5 py-1.5 rounded-lg text-xs font-bold bg-brand-600 text-white shadow-xs">
                    🌐 {{ $libro->idiomaOriginal ? $libro->idiomaOriginal->nombre : 'Original' }}
                </button>
                @foreach($ensenanza->traducciones as $trad)
                    <button onclick="switchReaderLang('lang-{{ $trad->idioma_id }}', this)" class="reader-lang-tab px-3.5 py-1.5 rounded-lg text-xs font-bold bg-slate-100 text-slate-600 hover:bg-slate-200">
                        {{ $trad->idioma ? $trad->idioma->nombre : 'Idioma' }} ({{ $trad->idioma ? strtoupper($trad->idioma->codigo) : '' }})
                    </button>
                @endforeach
            </div>

            <!-- Text Content -->
            <div class="p-6 sm:p-8 bg-slate-50 rounded-2xl border border-slate-100 text-slate-800 leading-relaxed font-serif text-lg">
                <div id="reader-text-original" class="reader-content-block">
                    "{{ $ensenanza->texto_original }}"
                </div>

                @foreach($ensenanza->traducciones as $trad)
                    <div id="reader-text-lang-{{ $trad->idioma_id }}" class="reader-content-block hidden" data-langcode="{{ $trad->idioma ? $trad->idioma->codigo : 'es' }}">
                        "{{ $trad->texto_traducido }}"
                    </div>
                @endforeach
            </div>

        </div>
    @else
        <div class="text-center py-12 bg-white rounded-3xl border border-slate-200">
            <p class="text-slate-500 text-sm">Este libro aún no tiene lecciones extraídas.</p>
        </div>
    @endif

</div>

@push('scripts')
<script>
    let currentReaderLangCode = 'es';

    function switchReaderLang(targetId, btn) {
        document.querySelectorAll('.reader-content-block').forEach(b => b.classList.add('hidden'));
        
        const targetElement = document.getElementById('reader-text-' + targetId);
        if (targetElement) {
            targetElement.classList.remove('hidden');
            currentReaderLangCode = targetElement.getAttribute('data-langcode') || 'es';
        }

        document.querySelectorAll('.reader-lang-tab').forEach(t => {
            t.classList.remove('bg-brand-600', 'text-white');
            t.classList.add('bg-slate-100', 'text-slate-600');
        });

        btn.classList.remove('bg-slate-100', 'text-slate-600');
        btn.classList.add('bg-brand-600', 'text-white');
    }

    function playReaderAudio() {
        const visibleBlock = document.querySelector('.reader-content-block:not(.hidden)');
        if (!visibleBlock || !('speechSynthesis' in window)) {
            alert('Tu navegador no soporta síntesis de voz.');
            return;
        }

        const text = visibleBlock.innerText.replace(/["']/g, '');
        const utterance = new SpeechSynthesisUtterance(text);
        
        const langMap = {
            'es': 'es-ES',
            'en': 'en-US',
            'pt': 'pt-BR',
            'fr': 'fr-FR',
            'it': 'it-IT',
            'zh': 'zh-CN',
            'zh-tw': 'zh-TW',
            'de': 'de-DE'
        };
        utterance.lang = langMap[currentReaderLangCode] || 'es-ES';
        utterance.rate = 0.95;

        window.speechSynthesis.cancel();
        window.speechSynthesis.speak(utterance);
    }
</script>
@endpush
@endsection