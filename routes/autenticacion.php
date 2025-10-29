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
use App\Administracion\Controllers\BitacoraController;

// Login/logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Dashboard protegido (vista segun rol)
Route::get('/dashboard', function () {
    $user = Auth::user();
    $role = strtolower((string) optional($user->rol)->nombre);

    if ($role === 'administrador') {
        $activos = function ($q) { $q->where('estado', true)->orWhereNull('estado'); };
        $usuariosCount = App\AutenticacionYSeguridad\Models\Usuario::where($activos)->count();
        $docentesCount = App\AutenticacionYSeguridad\Models\Usuario::whereHas('rol', function ($q) {
            $q->where('nombre', 'Docente');
        })->where($activos)->count();
        $administrativosCount = App\AutenticacionYSeguridad\Models\Usuario::whereHas('rol', function ($q) {
            $q->whereIn('nombre', ['Administrador', 'Coordinador', 'Autoridad']);
        })->where($activos)->count();
        return view('dashboard_admin', compact('user','usuariosCount','docentesCount','administrativosCount'));
    }

    if ($role === 'autoridad') {
        return view('dashboard_autoridad', compact('user'));
    }

    // Coordinador y demas roles (por defecto Docente):
    $cargas = App\GestionAcademica\Models\CargaHoraria::with(['materia','grupo'])
        ->where('id_usuario', $user->id_usuario)
        ->orderByDesc('gestion')
        ->get();
    if ($role === 'coordinador') { return view('dashboard_coordinador', compact('user')); } return view('dashboard_docente', compact('user','cargas'));
})->middleware('auth')->name('dashboard');

// Password reset (broker de Laravel)
Route::get('/forgot-password', [PasswordResetController::class, 'showLinkRequestForm'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLinkEmail'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordResetController::class, 'reset'])->middleware('guest')->name('password.update');

// Administracion (solo Administrador)
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

    // Bitacora
    Route::get('/bitacora', [BitacoraController::class, 'index'])->name('admin.bitacora.index');
});

// Decano: consultar bitacora (lectura)
Route::get('/autoridad/bitacora', [BitacoraController::class, 'index'])
    ->middleware(['auth','role:Autoridad,Administrador'])
    ->name('autoridad.bitacora.index');


