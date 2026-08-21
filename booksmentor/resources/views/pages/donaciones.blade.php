@extends('layouts.public')

@section('title', 'Donaciones y Apoyo — BooksMentor')

@section('content')
<div class="bg-slate-50 py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-rose-500 to-amber-500 text-white flex items-center justify-center mx-auto mb-6 text-2xl shadow-lg shadow-rose-500/20">
            ☕
        </div>

        <h1 class="text-4xl font-black text-slate-900 tracking-tight">Apoya el Desarrollo de BooksMentor</h1>
        <p class="text-lg text-slate-600 mt-3 max-w-2xl mx-auto leading-relaxed">
            BooksMentor es un proyecto independiente que busca democratizar el acceso a la sabiduría y hábitos positivos del mundo a través de la Inteligencia Artificial.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-12 text-left">
            <!-- Cafecito Card (Argentina) -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-3xl">🇦🇷</span>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Cafecito</h3>
                            <span class="text-xs text-slate-500">Para Argentina (MercadoPago)</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        Si estás en Argentina, puedes colaborar con un cafecito a través de MercadoPago para ayudarnos a mantener los servidores y APIs de traducción.
                    </p>
                </div>

                <a href="https://cafecito.app" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 py-3 px-6 bg-[#0088cc] hover:bg-[#0077b5] text-white font-bold text-sm rounded-xl shadow-md transition-transform hover:-translate-y-0.5">
                    <span>☕ Invitame un Cafecito</span>
                </a>
            </div>

            <!-- Ko-fi Card (International) -->
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-200 flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-3xl">🌎</span>
                        <div>
                            <h3 class="text-xl font-bold text-slate-900">Ko-fi</h3>
                            <span class="text-xs text-slate-500">Internacional (PayPal / Tarjetas)</span>
                        </div>
                    </div>
                    <p class="text-sm text-slate-600 leading-relaxed mb-6">
                        Para donaciones internacionales de cualquier parte del mundo mediante tarjeta de crédito, débito o cuenta de PayPal sin comisiones extras.
                    </p>
                </div>

                <a href="https://ko-fi.com" target="_blank" rel="noopener noreferrer" class="inline-flex items-center justify-center gap-2 py-3 px-6 bg-[#ff5f5f] hover:bg-[#ff4242] text-white font-bold text-sm rounded-xl shadow-md transition-transform hover:-translate-y-0.5">
                    <span>❤️ Buy me a Coffee on Ko-fi</span>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection