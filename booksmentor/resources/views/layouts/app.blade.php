<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Mi Panel — BooksMentor')</title>

    <!-- Google Fonts: Inter + Noto Sans SC -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Noto+Sans+SC:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS -->
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
                        sans: ['Inter', 'Noto Sans SC', '-apple-system', 'sans-serif'],
                        cjk: ['"Noto Sans SC"', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        html[data-lang="zh"], html[data-lang="zh-TW"] { font-family: 'Noto Sans SC', 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col">

    <!-- Client Header / Navbar -->
    <header class="sticky top-0 z-40 bg-white border-b border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <!-- Logo & Brand -->
                <div class="flex items-center gap-6">
                    <a href="{{ route('dashboard.index') }}" class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-brand-600 flex items-center justify-center text-white font-bold shadow-md shadow-brand-500/20">
                            BM
                        </div>
                        <span class="text-lg font-black tracking-tight text-slate-900">Books<span class="text-brand-600">Mentor</span></span>
                    </a>

                    <!-- Nav items desktop -->
                    <nav class="hidden md:flex items-center space-x-1 text-xs font-semibold text-slate-600">
                        <a href="{{ route('dashboard.index') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-brand-600 transition-colors {{ request()->routeIs('dashboard.index') ? 'bg-brand-50 text-brand-600 font-bold' : '' }}">
                            📊 Dashboard
                        </a>
                        <a href="{{ route('dashboard.suscripciones') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-brand-600 transition-colors {{ request()->routeIs('dashboard.suscripciones*') ? 'bg-brand-50 text-brand-600 font-bold' : '' }}">
                            📚 Mis Libros
                        </a>
                        <a href="{{ route('dashboard.explorar') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-brand-600 transition-colors {{ request()->routeIs('dashboard.explorar*') ? 'bg-brand-50 text-brand-600 font-bold' : '' }}">
                            🔍 Explorar
                        </a>
                        <a href="{{ route('dashboard.libros.crear') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-brand-600 transition-colors {{ request()->routeIs('dashboard.libros.crear') ? 'bg-brand-50 text-brand-600 font-bold' : '' }}">
                            ✨ Cargar Libro IA
                        </a>
                        <a href="{{ route('dashboard.sugerencias') }}" class="px-3 py-2 rounded-lg hover:bg-slate-100 hover:text-brand-600 transition-colors {{ request()->routeIs('dashboard.sugerencias*') ? 'bg-brand-50 text-brand-600 font-bold' : '' }}">
                            💡 Sugerencias
                        </a>
                    </nav>
                </div>

                <!-- Right items: Language, User Menu -->
                <div class="flex items-center space-x-3">
                    
                    <!-- Language selector -->
                    <div class="relative" id="lang-menu-wrapper">
                        @php
                            $currentLocale = app()->getLocale();
                            $flags = [
                                'es' => ['name' => 'Español', 'flag' => '🇪🇸'],
                                'en' => ['name' => 'English', 'flag' => '🇺🇸'],
                                'pt' => ['name' => 'Português', 'flag' => '🇧🇷'],
                                'fr' => ['name' => 'Français', 'flag' => '🇫🇷'],
                                'it' => ['name' => 'Italiano', 'flag' => '🇮🇹'],
                                'zh' => ['name' => '中文', 'flag' => '🇨🇳'],
                                'zh-TW' => ['name' => '繁體', 'flag' => '🇹🇼'],
                                'de' => ['name' => 'Deutsch', 'flag' => '🇩🇪'],
                            ];
                            $active = $flags[$currentLocale] ?? $flags['es'];
                        @endphp
                        <button onclick="document.getElementById('lang-dropdown').classList.toggle('hidden')" class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-medium text-slate-700 hover:bg-slate-50">
                            <span>{{ $active['flag'] }}</span>
                            <span class="hidden sm:inline">{{ $active['name'] }}</span>
                        </button>
                        <div id="lang-dropdown" class="hidden absolute right-0 mt-1.5 w-40 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50">
                            @foreach($flags as $code => $data)
                                <a href="{{ route('lang.switch', $code) }}" class="flex items-center gap-2 px-3 py-1.5 text-xs text-slate-700 hover:bg-brand-50 hover:text-brand-700 {{ $currentLocale == $code ? 'font-bold bg-slate-50' : '' }}">
                                    <span>{{ $data['flag'] }}</span>
                                    <span>{{ $data['name'] }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- User dropdown -->
                    <div class="relative">
                        <div class="flex items-center gap-2.5 pl-2 border-l border-slate-200">
                            <a href="{{ route('dashboard.perfil') }}" class="flex items-center gap-2 group">
                                <div class="w-8 h-8 rounded-full bg-brand-100 text-brand-700 font-bold flex items-center justify-center text-xs">
                                    {{ substr(Auth::user()->name, 0, 2) }}
                                </div>
                                <span class="hidden sm:inline text-xs font-bold text-slate-700 group-hover:text-brand-600">{{ Auth::user()->name }}</span>
                            </a>
                            
                            <form action="{{ route('logout') }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Cerrar Sesión">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </header>

    <!-- Global Flash Alerts -->
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

    @if(session('info'))
        <div class="bg-blue-50 border-b border-blue-200 px-4 py-3 text-blue-800 text-sm flex items-center justify-between max-w-7xl mx-auto w-full">
            <div class="flex items-center gap-2">
                <span>ℹ</span>
                <span>{{ session('info') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-blue-600 font-bold">✕</button>
        </div>
    @endif

    <!-- Main View -->
    <main class="flex-grow py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-2">
            <p>&copy; {{ date('Y') }} BooksMentor — Sabiduría Diaria con IA.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('dashboard.sugerencias') }}" class="hover:text-brand-600">Enviar Feedback</a>
                <span>·</span>
                <a href="{{ route('donaciones') }}" class="hover:text-brand-600">Apoyar con Cafecito/Ko-fi</a>
            </div>
        </div>
    </footer>

    <script>
        document.addEventListener('click', function(e) {
            const w = document.getElementById('lang-menu-wrapper');
            const d = document.getElementById('lang-dropdown');
            if (w && d && !w.contains(e.target)) {
                d.classList.add('hidden');
            }
        });
    </script>
    @stack('scripts')
</body>
</html>