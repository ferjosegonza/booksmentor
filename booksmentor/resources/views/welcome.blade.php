@extends('layouts.public')

@section('title', __('messages.app_name') . ' — ' . __('messages.hero_title'))
@section('description', __('messages.hero_subtitle'))

@section('content')
<!-- Hero Section -->
<section class="relative overflow-hidden bg-gradient-to-b from-indigo-50/50 via-white to-slate-50 pt-16 pb-20 lg:pt-24 lg:pb-32">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            
            <!-- Hero Text -->
            <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-brand-100/80 border border-brand-200 text-brand-800 text-xs font-bold uppercase tracking-wider">
                    <span>✨</span>
                    <span>Inteligencia Artificial + Hábitos Diarios</span>
                </div>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-[1.1]">
                    {{ __('messages.hero_title') }}
                </h1>

                <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto lg:mx-0 font-normal leading-relaxed">
                    {{ __('messages.hero_subtitle') }}
                </p>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 bg-brand-600 hover:bg-brand-700 text-white font-bold text-base rounded-xl shadow-lg shadow-brand-500/25 hover:shadow-brand-500/40 hover:-translate-y-0.5 transition-all text-center">
                        {{ __('messages.start_now') }} →
                    </a>
                    <a href="#demo-section" class="w-full sm:w-auto px-6 py-4 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-semibold text-base rounded-xl shadow-sm hover:border-slate-300 transition-all text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-brand-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                        <span>{{ __('messages.view_demo') }}</span>
                    </a>
                </div>

                <!-- Stats / Trust signals -->
                <div class="pt-8 border-t border-slate-200/80 flex items-center justify-center lg:justify-start gap-8 text-slate-600 text-sm">
                    <div>
                        <span class="block text-2xl font-black text-slate-900">100%</span>
                        <span class="text-xs text-slate-500 font-medium">Automatizado</span>
                    </div>
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div>
                        <span class="block text-2xl font-black text-slate-900">+8 Idiomas</span>
                        <span class="text-xs text-slate-500 font-medium">Traducción LLM</span>
                    </div>
                    <div class="h-8 w-px bg-slate-200"></div>
                    <div>
                        <span class="block text-2xl font-black text-slate-900">0 Spam</span>
                        <span class="text-xs text-slate-500 font-medium">Solo Sabiduría</span>
                    </div>
                </div>
            </div>

            <!-- Hero Interactive Preview Card -->
            <div class="lg:col-span-5">
                <div class="relative mx-auto max-w-md bg-white rounded-2xl shadow-xl shadow-slate-200/60 border border-slate-100 p-6">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                            <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                            <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                        </div>
                        <span class="text-xs font-mono text-slate-400">📬 Email Demo</span>
                    </div>

                    <div class="mt-4 space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>De: <strong>BooksMentor</strong> &lt;daily@booksmentor.com&gt;</span>
                            <span>Hoy, 08:00 AM</span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900">📖 Hábitos Atómicos — Lección #1: El poder del 1%</h3>
                        
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-sm text-slate-700 leading-relaxed">
                            <p class="font-medium text-brand-900 mb-1">🇪🇸 Español (Original):</p>
                            <p class="mb-3">"No te concentres en metas grandes; concéntrate en mejorar un 1% cada día. Las mejoras marginales se acumulan exponencialmente."</p>

                            <p class="font-medium text-brand-900 mb-1">🇺🇸 English (AI):</p>
                            <p class="mb-3">"Do not focus on massive goals; focus on improving by 1% each day. Marginal gains compound exponentially over time."</p>

                            <p class="font-medium text-brand-900 mb-1">🇨🇳 中文 (AI):</p>
                            <p>"不要只专注于宏伟的目标，每天进步1%即可。微小的改进会随着时间呈指数级累积。"</p>
                        </div>

                        <div class="pt-2 flex items-center justify-between text-xs">
                            <span class="text-emerald-600 font-semibold">✓ Entregado con éxito</span>
                            <span class="text-slate-400">Próximo: Mañana</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Interactive Reader Demo Section -->
<section id="demo-section" class="py-16 bg-white border-y border-slate-200">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ __('messages.interactive_reader') }}</h2>
            <p class="text-slate-600 mt-2">Prueba cómo se siente recibir y leer una lección en múltiples idiomas traducida con IA.</p>
        </div>

        @php
            $demoLibro = $librosDestacados->first();
            $demoEnsenanza = $demoLibro ? $demoLibro->ensenanzas->first() : null;
        @endphp

        @if($demoLibro && $demoEnsenanza)
            <div class="bg-slate-900 text-white rounded-3xl p-6 sm:p-10 shadow-2xl border border-slate-800" x-data="{ lang: 'es' }">
                <!-- Book header -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-800">
                    <div>
                        <span class="text-xs font-mono text-brand-400 uppercase tracking-wider font-semibold">Lección #{{ $demoEnsenanza->orden }} · {{ $demoLibro->titulo }}</span>
                        <h3 class="text-2xl font-bold text-white mt-1">{{ $demoEnsenanza->tema }}</h3>
                        <p class="text-xs text-slate-400">por {{ $demoLibro->autor }}</p>
                    </div>

                    <!-- Audio Text-to-Speech Button -->
                    <button id="tts-btn" onclick="playCurrentDemoAudio()" class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-500 text-white text-xs font-bold rounded-xl shadow-md transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                        <span>{{ __('messages.listen_audio') }}</span>
                    </button>
                </div>

                <!-- Language Tabs -->
                <div class="flex flex-wrap gap-2 pt-6">
                    <button onclick="setDemoLang('es', this)" class="demo-tab-btn active px-3.5 py-1.5 rounded-lg text-xs font-bold bg-brand-600 text-white transition-all">🇪🇸 Español</button>
                    <button onclick="setDemoLang('en', this)" class="demo-tab-btn px-3.5 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">🇺🇸 English</button>
                    <button onclick="setDemoLang('pt', this)" class="demo-tab-btn px-3.5 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">🇧🇷 Português</button>
                    <button onclick="setDemoLang('fr', this)" class="demo-tab-btn px-3.5 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">🇫🇷 Français</button>
                    <button onclick="setDemoLang('zh', this)" class="demo-tab-btn px-3.5 py-1.5 rounded-lg text-xs font-bold bg-slate-800 text-slate-300 hover:bg-slate-700 transition-all">🇨🇳 中文</button>
                </div>

                <!-- Teaching Content Box -->
                <div class="mt-6 p-6 sm:p-8 bg-slate-800/60 rounded-2xl border border-slate-700/60">
                    <p id="demo-teaching-text" class="text-lg sm:text-xl text-slate-100 font-serif leading-relaxed italic">
                        "{{ $demoEnsenanza->texto_original }}"
                    </p>
                </div>

                <!-- Hidden translations data -->
                <div id="demo-translations-store" class="hidden"
                     data-es="{{ $demoEnsenanza->texto_original }}"
                     data-en="{{ $demoEnsenanza->getTextoEnIdioma(2) ?? 'Do not focus on massive goals; focus on improving by 1% each day.' }}"
                     data-pt="{{ $demoEnsenanza->getTextoEnIdioma(3) ?? 'Não se concentre em grandes objetivos; concentre-se em melhorar 1% a cada dia.' }}"
                     data-fr="{{ $demoEnsenanza->getTextoEnIdioma(5) ?? 'Ne vous concentrez pas sur de grands objectifs ; concentrez-vous sur une amélioration de 1 % chaque jour.' }}"
                     data-zh="{{ $demoEnsenanza->getTextoEnIdioma(7) ?? '不要只专注于宏伟的目标，每天进步1%即可。微小的改进会随着时间呈指数级累积。' }}">
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Featured Books Showcase -->
<section class="py-16 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end justify-between mb-10">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ __('messages.featured_books') }}</h2>
                <p class="text-slate-600 mt-1">Explora libros listos para suscribirte y aprender cada día.</p>
            </div>
            <a href="{{ route('explorar') }}" class="mt-4 sm:mt-0 text-sm font-bold text-brand-600 hover:text-brand-700">
                Ver todos los libros →
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($librosDestacados as $libro)
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div class="p-6">
                        <div class="flex items-center gap-2 mb-3">
                            @foreach($libro->tags as $tag)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-brand-50 text-brand-700">
                                    {{ $tag->icono }} {{ $tag->nombre }}
                                </span>
                            @endforeach
                            <span class="text-xs font-medium text-slate-400 ml-auto">
                                🌐 {{ $libro->idiomaOriginal ? $libro->idiomaOriginal->nombre : 'Español' }}
                            </span>
                        </div>

                        <h3 class="text-xl font-bold text-slate-900 mb-1">{{ $libro->titulo }}</h3>
                        <p class="text-sm text-slate-500 font-medium mb-3">por {{ $libro->autor }}</p>
                        <p class="text-sm text-slate-600 line-clamp-3 leading-relaxed mb-4">
                            {{ $libro->descripcion ?: 'Resumen estructurado y lecciones diarias extraídas con IA.' }}
                        </p>
                    </div>

                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-600">📚 {{ $libro->cantidad_ensenanzas }} Lecciones</span>
                        <a href="{{ route('libro.detalle', $libro->id) }}" class="px-3.5 py-1.5 text-xs font-bold text-brand-600 bg-white border border-brand-200 rounded-lg hover:bg-brand-50 transition-colors">
                            {{ __('messages.read_teaching') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Pricing Plans Table -->
<section class="py-16 bg-white border-t border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-12">
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">{{ __('messages.pricing') }}</h2>
            <p class="text-slate-600 mt-2">Elige el plan que mejor se adapte a tu ritmo de aprendizaje y lectura.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            @foreach($planes as $plan)
                <div class="rounded-2xl border {{ $plan->id == 3 ? 'border-brand-500 shadow-xl ring-2 ring-brand-500/20 bg-brand-50/20' : 'border-slate-200 shadow-sm bg-white' }} p-6 flex flex-col justify-between">
                    <div>
                        @if($plan->id == 3)
                            <span class="px-3 py-1 bg-brand-600 text-white text-[11px] font-extrabold uppercase tracking-wider rounded-full self-start mb-3 inline-block">Popular</span>
                        @endif
                        <h3 class="text-xl font-bold text-slate-900">{{ $plan->nombre }}</h3>
                        <div class="mt-4 mb-6">
                            <span class="text-4xl font-black text-slate-900">${{ number_format($plan->precio_mensual, 0) }}</span>
                            <span class="text-xs text-slate-500 font-medium">/ mes</span>
                        </div>

                        <ul class="space-y-3 text-sm text-slate-600 mb-8">
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Hasta <strong>{{ $plan->max_libros >= 999 ? 'Ilimitados' : $plan->max_libros }}</strong> libro(s) activo(s)</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Hasta <strong>{{ $plan->max_idiomas }}</strong> idioma(s) por libro</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="{{ $plan->permite_audio ? 'text-emerald-500' : 'text-slate-300' }} font-bold">{{ $plan->permite_audio ? '✓' : '—' }}</span>
                                <span class="{{ $plan->permite_audio ? 'text-slate-700 font-medium' : 'text-slate-400' }}">Audio Text-to-Speech</span>
                            </li>
                            <li class="flex items-center gap-2">
                                <span class="text-emerald-500 font-bold">✓</span>
                                <span>Traducción instantánea con LLM</span>
                            </li>
                        </ul>
                    </div>

                    <a href="{{ route('register', ['plan_id' => $plan->id]) }}" class="w-full py-2.5 px-4 text-center text-sm font-bold rounded-xl {{ $plan->id == 3 ? 'bg-brand-600 hover:bg-brand-700 text-white shadow-md' : 'bg-slate-100 hover:bg-slate-200 text-slate-800' }} transition-colors">
                        Elegir {{ $plan->nombre }}
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Suggest a Book / Feedback Section -->
<section class="py-16 bg-slate-50 border-t border-slate-200">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl font-black text-slate-900 tracking-tight">¿Tienes un libro en mente?</h2>
        <p class="text-slate-600 mt-2 mb-8">Envíanos el título o sugerencia y nuestro motor de IA lo procesará.</p>

        <form action="{{ route('sugerir.store') }}" method="POST" class="bg-white p-6 sm:p-8 rounded-2xl shadow-sm border border-slate-200 text-left space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tu Email *</label>
                    <input type="email" name="email" value="{{ auth()->check() ? auth()->user()->email : old('email') }}" required class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="tu@email.com">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Tipo de Mensaje</label>
                    <select name="tipo_id" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        @foreach($tiposSugerencia as $t)
                            <option value="{{ $t->id }}">{{ $t->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Libro Sugerido (opcional)</label>
                <input type="text" name="libro_sugerido" class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="Ej: Meditaciones de Marco Aurelio">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Mensaje o Comentarios *</label>
                <textarea name="mensaje" rows="3" required class="w-full px-3.5 py-2 text-sm border border-slate-200 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="¿Por qué te gustaría ver este libro o qué mejora sugieres?"></textarea>
            </div>

            <button type="submit" class="w-full py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-xl shadow-md transition-colors">
                Enviar Sugerencia
            </button>
        </form>
    </div>
</section>

@push('scripts')
<script>
    let currentDemoLang = 'es';

    function setDemoLang(langCode, btn) {
        currentDemoLang = langCode;
        const store = document.getElementById('demo-translations-store');
        const textElement = document.getElementById('demo-teaching-text');

        if (store && textElement) {
            const text = store.getAttribute('data-' + langCode) || store.getAttribute('data-es');
            textElement.innerText = '"' + text + '"';
        }

        document.querySelectorAll('.demo-tab-btn').forEach(b => {
            b.classList.remove('bg-brand-600', 'text-white');
            b.classList.add('bg-slate-800', 'text-slate-300');
        });

        btn.classList.remove('bg-slate-800', 'text-slate-300');
        btn.classList.add('bg-brand-600', 'text-white');
    }

    function playCurrentDemoAudio() {
        const textElement = document.getElementById('demo-teaching-text');
        if (!textElement || !('speechSynthesis' in window)) {
            alert('Tu navegador no soporta síntesis de voz.');
            return;
        }

        const text = textElement.innerText;
        const utterance = new SpeechSynthesisUtterance(text);
        
        const langMap = {
            'es': 'es-ES',
            'en': 'en-US',
            'pt': 'pt-BR',
            'fr': 'fr-FR',
            'zh': 'zh-CN'
        };
        utterance.lang = langMap[currentDemoLang] || 'es-ES';
        utterance.rate = 0.95;

        window.speechSynthesis.cancel(); // Stop any ongoing speech
        window.speechSynthesis.speak(utterance);
    }
</script>
@endpush
@endsection