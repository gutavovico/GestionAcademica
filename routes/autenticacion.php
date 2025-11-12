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
use App\Administracion\Controllers\PanelAdminController;
use App\ReportesYEstadisticas\Controllers\ReportesGlobalesController;
use App\ReportesYEstadisticas\Controllers\ReporteHorariosAsistenciaController;
use App\ControlAsistencias\Controllers\HorarioSemanalController;
use App\ControlAsistencias\Controllers\HistorialAsistenciaController;
use App\ControlAsistencias\Controllers\RegistroAsistenciaController;

// Login/logout
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Dashboard protegido (vista segun rol)
Route::get('/dashboard', function () {
    $user = Auth::user();
    $role = strtolower((string) optional($user->rol)->nombre);

    if ($role === 'administrador') {
        return app(PanelAdminController::class)->dashboard();
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
    // Panel administrativo general (CU20)
    Route::get('/panel', [PanelAdminController::class, 'dashboard'])->name('admin.panel');
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

// Alias para compatibilidad: /decano/bitacora -> /autoridad/bitacora
Route::get('/decano/bitacora', function () {
    return redirect('/autoridad/bitacora');
})->middleware(['auth','role:Autoridad,Administrador']);

// Reportes estadísticos globales (CU22)
Route::get('/reportes/globales', [ReportesGlobalesController::class, 'vista'])
    ->middleware(['auth','role:Autoridad,Administrador'])
    ->name('reportes.globales.vista');
Route::get('/reportes/globales/opciones', [ReportesGlobalesController::class, 'opciones'])
    ->middleware(['auth','role:Autoridad,Administrador'])
    ->name('reportes.globales.opciones');
Route::get('/reportes/globales/data', [ReportesGlobalesController::class, 'data'])
    ->middleware(['auth','role:Autoridad,Administrador'])
    ->name('reportes.globales.data');

// CU16: Reporte de horarios y asistencia (Administrador / Coordinador)
Route::get('/reportes/horarios-asistencia', [ReporteHorariosAsistenciaController::class, 'vista'])
    ->middleware(['auth','role:Administrador,Coordinador'])
    ->name('reportes.horarios_asistencia.vista');
Route::get('/reportes/horarios-asistencia/opciones', [ReporteHorariosAsistenciaController::class, 'opciones'])
    ->middleware(['auth','role:Administrador,Coordinador'])
    ->name('reportes.horarios_asistencia.opciones');
Route::get('/reportes/horarios-asistencia/data', [ReporteHorariosAsistenciaController::class, 'data'])
    ->middleware(['auth','role:Administrador,Coordinador'])
    ->name('reportes.horarios_asistencia.data');


// Docente: visualizar horario semanal (CU12)
Route::get('/docente/horario-semanal', [HorarioSemanalController::class, 'vista'])
    ->middleware(['auth','role:Docente'])
    ->name('docente.horario.semanal');

Route::get('/docente/mi-horario-semanal', [HorarioSemanalController::class, 'miHorario'])
    ->middleware(['auth','role:Docente'])
    ->name('docente.horario.semanal.data');

// Historial de asistencia (CU14)
Route::get('/docente/mi-historial-asistencia', [HistorialAsistenciaController::class, 'miHistorial'])
    ->middleware(['auth','role:Docente,Coordinador,Administrador'])
    ->name('docente.historial.asistencia.data');

// Coordinador consulta por docente
Route::get('/coordinador/historial-asistencia', [HistorialAsistenciaController::class, 'buscar'])
    ->middleware(['auth','role:Coordinador,Administrador'])
    ->name('coordinador.historial.asistencia.data');

// CU13: Registrar asistencia docente
Route::get('/docente/registrar-asistencia', [RegistroAsistenciaController::class, 'vistaDocente'])
    ->middleware(['auth','role:Docente'])
    ->name('docente.asistencia.vista');
Route::get('/docente/horarios-hoy', [RegistroAsistenciaController::class, 'horariosHoy'])
    ->middleware(['auth','role:Docente'])
    ->name('docente.asistencia.hoy');
Route::post('/docente/registrar-asistencia', [RegistroAsistenciaController::class, 'registrarDocente'])
    ->middleware(['auth','role:Docente'])
    ->name('docente.asistencia.registrar');
Route::post('/admin/registrar-asistencia', [RegistroAsistenciaController::class, 'registrarAdmin'])
    ->middleware(['auth','role:Administrador'])
    ->name('admin.asistencia.registrar');
Route::get('/admin/control-asistencia', [RegistroAsistenciaController::class, 'vistaAdmin'])
    ->middleware(['auth','role:Administrador'])
    ->name('admin.asistencia.vista');
Route::get('/admin/horarios-hoy', [RegistroAsistenciaController::class, 'horariosHoyAdmin'])
    ->middleware(['auth','role:Administrador'])
    ->name('admin.asistencia.hoy');
