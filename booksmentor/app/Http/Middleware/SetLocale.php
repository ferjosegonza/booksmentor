<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $supported = ['es', 'en', 'pt', 'it', 'fr', 'zh', 'zh-TW', 'de'];

        if ($request->has('lang') && in_array($request->get('lang'), $supported)) {
            $lang = $request->get('lang');
            Session::put('locale', $lang);
        } elseif (Session::has('locale')) {
            $lang = Session::get('locale');
        } else {
            // Check browser preferred language
            $browserLang = substr($request->server('HTTP_ACCEPT_LANGUAGE', 'es'), 0, 2);
            $lang = in_array($browserLang, $supported) ? $browserLang : 'es';
        }

        App::setLocale($lang);
        return $next($request);
    }
}