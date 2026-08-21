<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Debes iniciar sesión para acceder al panel de administración.');
        }

        if (!Auth::user()->isAdmin()) {
            return redirect()->route('dashboard.index')->with('error', 'No tienes permisos de administrador.');
        }

        return $next($request);
    }
}