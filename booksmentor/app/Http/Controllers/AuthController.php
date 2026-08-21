<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use App\Models\User;
use App\Models\Usuario;
use App\Models\CatPlane;
use App\Models\CatFrecuencia;
use App\Models\CatIdioma;
use App\Models\Libro;
use App\Models\Suscripcion;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin() ? redirect()->route('admin.dashboard') : redirect()->route('dashboard.index');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Ensure matching Usuario profile exists
            $user = Auth::user();
            $usuario = Usuario::where('email', $user->email)->orWhere('user_id', $user->id)->first();
            if (!$usuario) {
                Usuario::create([
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'nombre' => $user->name,
                    'frecuencia_id' => 1,
                    'plan_id' => 1,
                    'hora_envio' => '08:00:00',
                    'zona_horaria' => 'America/Argentina/Buenos_Aires',
                    'activo' => true,
                ]);
            } else if (!$usuario->user_id) {
                $usuario->update(['user_id' => $user->id]);
            }

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'))->with('success', '¡Bienvenido al Panel de Administración!');
            }

            return redirect()->intended(route('dashboard.index'))->with('success', '¡Bienvenido de nuevo, ' . $user->name . '!');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }

        $planes = CatPlane::orderBy('orden')->get();
        $frecuencias = CatFrecuencia::orderBy('orden')->get();
        $idiomas = CatIdioma::where('activo', true)->get();
        $librosDestacados = Libro::where('activo', true)->with('idiomaOriginal')->take(6)->get();

        return view('auth.register', compact('planes', 'frecuencias', 'idiomas', 'librosDestacados'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'frecuencia_id' => ['nullable', 'exists:cat_frecuencias,id'],
            'plan_id' => ['nullable', 'exists:cat_planes,id'],
            'hora_envio' => ['nullable', 'string'],
            'zona_horaria' => ['nullable', 'string'],
            'idiomas' => ['nullable', 'array'],
            'libro_id' => ['nullable', 'exists:libros,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'user',
        ]);

        $usuario = Usuario::create([
            'user_id' => $user->id,
            'email' => $user->email,
            'nombre' => $user->name,
            'frecuencia_id' => $request->frecuencia_id ?: 1, // Diaria
            'plan_id' => $request->plan_id ?: 1, // Gratuito
            'hora_envio' => $request->hora_envio ?: '08:00:00',
            'zona_horaria' => $request->zona_horaria ?: 'America/Argentina/Buenos_Aires',
            'fecha_registro' => Carbon::now(),
            'activo' => true,
        ]);

        // Auto-subscribe to selected book if chosen
        if ($request->libro_id) {
            $sub = Suscripcion::create([
                'usuario_id' => $usuario->id,
                'libro_id' => $request->libro_id,
                'estado_id' => 1,
                'ultima_ensenanza_enviada' => 0,
                'fecha_proximo_envio' => Carbon::now()->addDay(),
            ]);

            $idiomaIds = $request->input('idiomas', [1]);
            $sub->idiomas()->sync($idiomaIds);
        } else {
            // Subscribe to first available sample book
            $sampleBook = Libro::first();
            if ($sampleBook) {
                $sub = Suscripcion::create([
                    'usuario_id' => $usuario->id,
                    'libro_id' => $sampleBook->id,
                    'estado_id' => 1,
                    'ultima_ensenanza_enviada' => 0,
                    'fecha_proximo_envio' => Carbon::now()->addDay(),
                ]);
                $sub->idiomas()->sync([1, 2]);
            }
        }

        Auth::login($user);

        return redirect()->route('dashboard.index')->with('success', '¡Registro completado con éxito! Bienvenido a BooksMentor.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('info', 'Has cerrado sesión.');
    }

    public function showForgotPassword()
    {
        return view('auth.forgot-password');
    }

    public function forgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // In local/demo mode or general mode, provide a friendly message and direct reset option
        $user = User::where('email', $request->email)->first();
        if ($user) {
            return back()->with('status', 'Hemos enviado las instrucciones para restablecer tu contraseña a tu correo electrónico.');
        }

        return back()->withErrors(['email' => 'No encontramos ninguna cuenta con ese correo electrónico.']);
    }
}