<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-lang="{{ app()->getLocale() }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('messages.app_name') . ' — ' . __('messages.tagline'))</title>
    <meta name="description" content="@yield('description', __('messages.hero_subtitle'))">
    <meta property="og:title" content="@yield('title', __('messages.app_name') . ' — ' . __('messages.tagline'))">
    <meta property="og:description" content="@yield('description', __('messages.hero_subtitle'))">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', __('messages.app_name'))">
    <meta name="twitter:description" content="@yield('description', __('messages.hero_subtitle'))">

    <!-- Google Fonts: Inter + Noto Sans SC for CJK Language Support -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+SC:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans SC', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        cjk: ['"Noto Sans SC"', 'sans-serif']
                    }
                }
            }
        }
    </script>

    <style>
        html[data-lang="zh"], html[data-lang="zh-TW"], html[data-lang="zh-CN"] {
            font-family: 'Noto Sans SC', 'Inter', sans-serif;
        }
        :focus-visible {
            outline: 2px solid #4f46e5;
            outline-offset: 2px;
        }
        .adsense-container {
            transition: max-height 0.3s ease, opacity 0.3s ease;
            min-height: 0;
            contain-intrinsic-size: 0 90px;
        }
        .adsense-container.collapsed {
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            padding: 0 !important;
            margin: 0 !important;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col selection:bg-brand-500 selection:text-white">

    <!-- Skip Link for Accessibility -->
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-brand-600 focus:text-white focus:rounded-md">
        Saltar al contenido principal
    </a>

    <!-- Top Collapsible Ad Container (Avoids CLS) -->
    <div id="top-ad-wrapper" class="w-full bg-slate-100 border-b border-slate-200 py-1 px-4 text-center relative adsense-container">
        <div class="max-w-4xl mx-auto flex items-center justify-between text-xs text-slate-400">
            <span class="uppercase tracking-wider">Publicidad</span>
            <div class="h-10 sm:h-12 flex items-center justify-center text-slate-400 font-mono text-xs">
                <span>Espacio para Anuncio Responsivo Google AdSense</span>
            </div>
            <button onclick="document.getElementById('top-ad-wrapper').classList.toggle('collapsed')" class="hover:text-slate-700 px-2 py-1" aria-label="Cerrar Anuncio">✕</button>
        </div>
    </div>

    <!-- Header / Navbar -->
    <header class="sticky top-0 z-40 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-20">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="flex items-center gap-3 group focus:ring-2 focus:ring-brand-500 rounded-lg p-1">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-brand-600 to-indigo-500 flex items-center justify-center text-white shadow-md shadow-brand-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div class="flex flex-col">
                        <span class="text-xl font-black tracking-tight text-slate-900">Books<span class="text-brand-600">Mentor</span></span>
                        <span class="text-[10px] tracking-wider uppercase font-semibold text-slate-400 -mt-1">AI Daily Wisdom</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center space-x-6 text-sm font-medium text-slate-600">
                    <a href="{{ route('explorar') }}" class="hover:text-brand-600 transition-colors {{ request()->routeIs('explorar') ? 'text-brand-600 font-semibold' : '' }}">{{ __('messages.explore') }}</a>
                    <a href="{{ route('planes') }}" class="hover:text-brand-600 transition-colors {{ request()->routeIs('planes') ? 'text-brand-600 font-semibold' : '' }}">{{ __('messages.pricing') }}</a>
                    <a href="{{ route('donaciones') }}" class="hover:text-brand-600 transition-colors {{ request()->routeIs('donaciones') ? 'text-brand-600 font-semibold' : '' }}">{{ __('messages.donations') }}</a>
                </nav>

                <!-- Actions & Language Switcher -->
                <div class="flex items-center space-x-3 sm:space-x-4">
                    
                    <!-- Language Selector (Flags + Names desktop, Compact globe mobile) -->
                    <div class="relative" id="lang-dropdown-wrapper">
                        @php
                            $currentLocale = app()->getLocale();
                            $flags = [
                                'es' => ['name' => 'Español', 'flag' => '🇪🇸'],
                                'en' => ['name' => 'English', 'flag' => '🇺🇸'],
                                'pt' => ['name' => 'Português', 'flag' => '🇧🇷'],
                                'fr' => ['name' => 'Français', 'flag' => '🇫🇷'],
                                'it' => ['name' => 'Italiano', 'flag' => '🇮🇹'],
                                'zh' => ['name' => '中文 (简体)', 'flag' => '🇨🇳'],
                                'zh-TW' => ['name' => '中文 (繁體)', 'flag' => '🇹🇼'],
                                'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪'],
                            ];
                            $active = $flags[$currentLocale] ?? $flags['es'];
                        @endphp
                        <button onclick="document.getElementById('lang-menu').classList.toggle('hidden')" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white hover:bg-slate-50 text-xs font-medium text-slate-700 shadow-sm focus:ring-2 focus:ring-brand-500" aria-label="Seleccionar idioma">
                            <span class="text-sm">{{ $active['flag'] }}</span>
                            <span class="hidden sm:inline">{{ $active['name'] }}</span>
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>

                        <div id="lang-menu" class="hidden absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-xl border border-slate-100 py-1.5 z-50">
                            @foreach($flags as $code => $data)
                                <a href="{{ route('lang.switch', $code) }}" class="flex items-center gap-2.5 px-3.5 py-2 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-700 {{ $currentLocale == $code ? 'font-bold bg-slate-50 text-brand-600' : '' }}">
                                    <span>{{ $data['flag'] }}</span>
                                    <span>{{ $data['name'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    @auth
                        @if(Auth::user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-slate-900 text-white hover:bg-slate-800 shadow-sm">Panel Admin</a>
                        @else
                            <a href="{{ route('dashboard.index') }}" class="px-3.5 py-2 text-xs font-semibold rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow-sm shadow-brand-500/20">{{ __('messages.dashboard') }}</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-semibold text-slate-700 hover:text-brand-600 px-2 py-1">{{ __('messages.login') }}</a>
                        <a href="{{ route('register') }}" class="px-3.5 py-2 text-xs font-bold rounded-lg bg-brand-600 text-white hover:bg-brand-700 shadow-sm shadow-brand-500/20 transition-all hover:scale-105">{{ __('messages.register') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Global Alerts -->
    @if(session('success'))
        <div class="bg-emerald-50 border-b border-emerald-200 px-4 py-3 text-emerald-800 text-sm flex items-center justify-between max-w-7xl mx-auto w-full">
            <div class="flex items-center gap-2">
                <span>✓</span>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-600 font-bold">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-rose-50 border-b border-rose-200 px-4 py-3 text-rose-800 text-sm flex items-center justify-between max-w-7xl mx-auto w-full">
            <div class="flex items-center gap-2">
                <span>✕</span>
                <span>{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-600 font-bold">✕</button>
        </div>
    @endif

    <!-- Main Content -->
    <main id="main-content" class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 pt-12 pb-8 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <!-- Brand Info -->
                <div class="space-y-4 md:col-span-2">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-brand-600 flex items-center justify-center text-white font-bold">BM</div>
                        <span class="text-lg font-black text-slate-900">Books<span class="text-brand-600">Mentor</span></span>
                    </div>
                    <p class="text-sm text-slate-500 max-w-sm">
                        {{ __('messages.tagline') }}
                    </p>

                    <!-- Official Donation Buttons (Cafecito & Ko-fi with responsive wrap) -->
                    <div class="pt-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-2">{{ __('messages.support_project') }}:</span>
                        <div class="flex flex-wrap items-center gap-3">
                            <!-- Cafecito (Argentina) Official Button -->
                            <a href="https://cafecito.app" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#0088cc] hover:bg-[#0077b5] text-white text-xs font-bold rounded-lg shadow-sm transition-transform hover:-translate-y-0.5" title="Invitame un Cafecito (Argentina)">
                                <span>☕ Cafecito</span>
                            </a>
                            <!-- Ko-fi (Internacional) Official Button -->
                            <a href="https://ko-fi.com" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#ff5f5f] hover:bg-[#ff4242] text-white text-xs font-bold rounded-lg shadow-sm transition-transform hover:-translate-y-0.5" title="Buy me a Coffee on Ko-fi (International)">
                                <span>❤️ Ko-fi</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-4">Plataforma</h4>
                    <ul class="space-y-2 text-sm text-slate-600">
                        <li><a href="{{ route('explorar') }}" class="hover:text-brand-600">{{ __('messages.explore') }}</a></li>
                        <li><a href="{{ route('planes') }}" class="hover:text-brand-600">{{ __('messages.pricing') }}</a></li>
                        <li><a href="{{ route('donaciones') }}" class="hover:text-brand-600">{{ __('messages.donations') }}</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-brand-600">{{ __('messages.login') }}</a></li>
                    </ul>
                </div>

                <!-- Languages Supported -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 mb-4">{{ __('messages.translations_available') }}</h4>
                    <div class="flex flex-wrap gap-1.5 text-xs text-slate-600">
                        <span class="px-2 py-0.5 bg-slate-100 rounded">Español</span>
                        <span class="px-2 py-0.5 bg-slate-100 rounded">English</span>
                        <span class="px-2 py-0.5 bg-slate-100 rounded">Português</span>
                        <span class="px-2 py-0.5 bg-slate-100 rounded">Français</span>
                        <span class="px-2 py-0.5 bg-slate-100 rounded">Italiano</span>
                        <span class="px-2 py-0.5 bg-slate-100 rounded">Deutsch</span>
                        <span class="px-2 py-0.5 bg-slate-100 rounded">中文</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Ad Container (Avoids CLS) -->
            <div id="bottom-ad-wrapper" class="w-full bg-slate-100 border border-slate-200 rounded-lg p-2 text-center my-6 relative adsense-container">
                <div class="flex items-center justify-between text-xs text-slate-400">
                    <span class="uppercase tracking-wider">Publicidad</span>
                    <div class="h-10 flex items-center justify-center text-slate-400 font-mono text-xs">
                        <span>Banner Google AdSense Responsive</span>
                    </div>
                    <button onclick="document.getElementById('bottom-ad-wrapper').classList.toggle('collapsed')" class="hover:text-slate-700 px-2" aria-label="Cerrar Anuncio">✕</button>
                </div>
            </div>

            <div class="border-t border-slate-100 pt-6 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400">
                <p>&copy; {{ date('Y') }} BooksMentor. Todos los derechos reservados.</p>
                <p class="mt-2 sm:mt-0">Impulsado por LLM & Laravel</p>
            </div>
        </div>
    </footer>

    <script>
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            const langWrapper = document.getElementById('lang-dropdown-wrapper');
            const langMenu = document.getElementById('lang-menu');
            if (langWrapper && langMenu && !langWrapper.contains(e.target)) {
                langMenu.classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>