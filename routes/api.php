<?php

use Illuminate\Support\Facades\Route;
use App\GestionAulasHorarios\Controllers\AulaController;
use App\GestionAulasHorarios\Controllers\AsignacionAulaController;
use App\GestionAulasHorarios\Controllers\HorarioController;

Route::post('/aulas/registrar', [AulaController::class, 'registrar']);

// CU8: Asignación de aulas a grupos
Route::get('/asignacion/aulas-disponibles', [AsignacionAulaController::class, 'disponibles']);
Route::post('/asignacion/asignar', [AsignacionAulaController::class, 'asignar']);
Route::get('/asignacion/aulas', [AsignacionAulaController::class, 'aulas']);
Route::get('/asignacion/slots', [AsignacionAulaController::class, 'slots']);
Route::get('/asignacion/modulos', [AsignacionAulaController::class, 'modulos']);

// CU11: CRUD Horarios del docente (soft-delete = marcar 'Activo')
Route::get('/horarios', [HorarioController::class, 'index']);
Route::post('/horarios', [HorarioController::class, 'store']);
// Especificos primero para no chocar con {id}
Route::get('/horarios/docentes', [HorarioController::class, 'docentes']);
Route::get('/horarios/cargas', [HorarioController::class, 'cargas']);
// Operaciones por ID (numérico)
Route::get('/horarios/{id}', [HorarioController::class, 'show'])->whereNumber('id');
Route::put('/horarios/{id}', [HorarioController::class, 'update'])->whereNumber('id');
Route::delete('/horarios/{id}', [HorarioController::class, 'destroy'])->whereNumber('id');
