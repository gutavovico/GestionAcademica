<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\AutenticacionYSeguridad\Controllers\AuthController;

// Ruta para mostrar el formulario de login (accesible a invitados)
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');

// Ruta para procesar el inicio de sesión (accesible a invitados)
Route::post('/login', [AuthController::class, 'login']);

// Ruta para cerrar la sesión (accesible solo a usuarios autenticados)
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Ruta protegida de Dashboard (vista Blade)
Route::get('/dashboard', function () {
    $user = Auth::user();
    return view('dashboard', compact('user'));
})->middleware('auth')->name('dashboard');

