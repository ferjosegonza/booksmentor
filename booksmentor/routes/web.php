<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Client\DashboardController as ClientDashboardController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminLibroController;
use App\Http\Controllers\Admin\AdminEnsenanzaController;
use App\Http\Controllers\Admin\AdminTraduccionController;
use App\Http\Controllers\Admin\AdminSuscripcionController;
use App\Http\Controllers\Admin\AdminUsuarioController;
use App\Http\Controllers\Admin\AdminSugerenciaController;
use App\Http\Controllers\Admin\AdminCatalogoController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/lang/{lang}', [HomeController::class, 'switchLanguage'])->name('lang.switch');
Route::get('/explorar', [HomeController::class, 'explorar'])->name('explorar');
Route::get('/libro/{id}', [HomeController::class, 'showLibro'])->name('libro.detalle');
Route::get('/donaciones', [HomeController::class, 'donaciones'])->name('donaciones');
Route::get('/planes', [HomeController::class, 'planes'])->name('planes');
Route::post('/sugerir', [HomeController::class, 'storeSugerencia'])->name('sugerir.store');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

/*
|--------------------------------------------------------------------------
| Client / User Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'client'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', [ClientDashboardController::class, 'index'])->name('index');
    Route::get('/explorar', [ClientDashboardController::class, 'explorar'])->name('explorar');
    Route::post('/suscribir', [ClientDashboardController::class, 'suscribir'])->name('suscribir');
    
    // User custom books upload with LLM
    Route::get('/mis-libros/crear', [ClientDashboardController::class, 'crearLibro'])->name('libros.crear');
    Route::post('/mis-libros/crear', [ClientDashboardController::class, 'guardarLibro'])->name('libros.guardar');
    Route::get('/mis-libros/bulk', [ClientDashboardController::class, 'bulkLibros'])->name('libros.bulk');
    Route::post('/mis-libros/bulk', [ClientDashboardController::class, 'guardarBulkLibros'])->name('libros.guardarBulk');

    // Subscriptions
    Route::get('/suscripciones', [ClientDashboardController::class, 'suscripciones'])->name('suscripciones');
    Route::post('/suscripciones/{id}/actualizar', [ClientDashboardController::class, 'actualizarSuscripcion'])->name('suscripciones.actualizar');
    Route::post('/suscripciones/{id}/pausar', [ClientDashboardController::class, 'pausarSuscripcion'])->name('suscripciones.pausar');
    Route::post('/suscripciones/{id}/enviar-prueba', [ClientDashboardController::class, 'enviarPruebaEmail'])->name('suscripciones.enviarPrueba');

    // Interactive Reader
    Route::get('/leer/{libroId}/{orden?}', [ClientDashboardController::class, 'leer'])->name('leer');

    // Suggestions / Feedback
    Route::get('/sugerencias', [ClientDashboardController::class, 'sugerencias'])->name('sugerencias');
    Route::post('/sugerencias', [ClientDashboardController::class, 'guardarSugerencia'])->name('sugerencias.guardar');

    // Profile & Preferences
    Route::get('/perfil', [ClientDashboardController::class, 'perfil'])->name('perfil');
    Route::post('/perfil', [ClientDashboardController::class, 'actualizarPerfil'])->name('perfil.actualizar');
});

/*
|--------------------------------------------------------------------------
| Admin Dashboard Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/configuracion', [AdminDashboardController::class, 'configuracion'])->name('configuracion');
    Route::post('/test-llm', [AdminDashboardController::class, 'testLLM'])->name('testLLM');
    Route::post('/test-translate', [AdminDashboardController::class, 'testTranslate'])->name('testTranslate');
    Route::post('/ejecutar-cron', [AdminDashboardController::class, 'ejecutarCron'])->name('ejecutarCron');

    // Books CRUD & LLM operations
    Route::get('/libros/bulk', [AdminLibroController::class, 'bulk'])->name('libros.bulk');
    Route::post('/libros/bulk', [AdminLibroController::class, 'storeBulk'])->name('libros.storeBulk');
    Route::post('/libros/{id}/toggle-activo', [AdminLibroController::class, 'toggleActivo'])->name('libros.toggleActivo');
    Route::post('/libros/{id}/traducir-faltantes', [AdminLibroController::class, 'traducirFaltantes'])->name('libros.traducirFaltantes');
    Route::resource('libros', AdminLibroController::class);

    // Teachings CRUD
    Route::post('/ensenanzas/{id}/traducir', [AdminEnsenanzaController::class, 'traducir'])->name('ensenanzas.traducir');
    Route::resource('ensenanzas', AdminEnsenanzaController::class);

    // Translations Management
    Route::post('/traducciones/{id}/regenerar', [AdminTraduccionController::class, 'regenerar'])->name('traducciones.regenerar');
    Route::resource('traducciones', AdminTraduccionController::class)->only(['index', 'edit', 'update', 'destroy']);

    // Subscriptions Management
    Route::post('/suscripciones/{id}/forzar-envio', [AdminSuscripcionController::class, 'forzarEnvio'])->name('suscripciones.forzarEnvio');
    Route::resource('suscripciones', AdminSuscripcionController::class)->only(['index', 'update', 'destroy']);

    // Users Management
    Route::resource('usuarios', AdminUsuarioController::class)->only(['index', 'edit', 'update', 'destroy']);

    // Suggestions / Feedback Inbox
    Route::post('/sugerencias/{id}/responder', [AdminSugerenciaController::class, 'responder'])->name('sugerencias.responder');
    Route::resource('sugerencias', AdminSugerenciaController::class)->only(['index', 'show', 'destroy']);

    // Catalogs ABM
    Route::get('/catalogos', [AdminCatalogoController::class, 'index'])->name('catalogos.index');
    Route::post('/catalogos/tags', [AdminCatalogoController::class, 'storeTag'])->name('catalogos.storeTag');
    Route::post('/catalogos/idiomas', [AdminCatalogoController::class, 'storeIdioma'])->name('catalogos.storeIdioma');
    Route::post('/catalogos/idiomas/{id}/toggle', [AdminCatalogoController::class, 'toggleIdioma'])->name('catalogos.toggleIdioma');
});