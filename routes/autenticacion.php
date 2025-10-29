<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\AutenticacionYSeguridad\Controllers\AuthController;
use App\AutenticacionYSeguridad\Controllers\PasswordResetController;
use App\AutenticacionYSeguridad\Controllers\UsuarioAdminController;
use App\AutenticacionYSeguridad\Controllers\RolAdminController;
use App\GestionAcademica\Controllers\MateriaController;
use App\GestionAcademica\Controllers\GrupoController;
use App\GestionAcademica\Controllers\CargaHorariaController;

// Login/logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Dashboard protegido (vista según rol)
Route::get('/dashboard', function () {
    $user = Auth::user();
    $view = strtolower((string) optional($user->rol)->nombre) === 'administrador' ? 'dashboard_admin' : 'dashboard_docente';
    return view($view, compact('user'));
})->middleware('auth')->name('dashboard');

// Password reset (broker de Laravel)
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');

// Administración (solo Administrador)
Route::middleware(['auth', 'role:Administrador'])->prefix('admin')->group(function () {
    // Usuarios
    Route::get('/usuarios', [UsuarioAdminController::class, 'index'])->name('admin.usuarios.index');
    Route::post('/usuarios', [UsuarioAdminController::class, 'store'])->name('admin.usuarios.store');
    Route::post('/usuarios/{id}', [UsuarioAdminController::class, 'update'])->name('admin.usuarios.update');
    Route::post('/usuarios/{id}/toggle', [UsuarioAdminController::class, 'toggleEstado'])->name('admin.usuarios.toggle');

    // Roles
    Route::get('/roles', [RolAdminController::class, 'index'])->name('admin.roles.index');
    Route::post('/roles', [RolAdminController::class, 'store'])->name('admin.roles.store');
    Route::post('/roles/{id}', [RolAdminController::class, 'update'])->name('admin.roles.update');
    Route::post('/roles/{id}/delete', [RolAdminController::class, 'destroy'])->name('admin.roles.destroy');

    // Materias
    Route::get('/materias', [MateriaController::class, 'index'])->name('admin.materias.index');
    Route::post('/materias', [MateriaController::class, 'store'])->name('admin.materias.store');

    // Grupos
    Route::get('/grupos', [GrupoController::class, 'index'])->name('admin.grupos.index');
    Route::post('/grupos', [GrupoController::class, 'store'])->name('admin.grupos.store');

    // Carga Horaria
    Route::get('/cargas', [CargaHorariaController::class, 'index'])->name('admin.cargas.index');
    Route::post('/cargas', [CargaHorariaController::class, 'store'])->name('admin.cargas.store');
});

