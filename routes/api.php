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

// CU11: CRUD Horarios del docente (soft-delete = marcar 'Activo')
Route::get('/horarios', [HorarioController::class, 'index']);
Route::post('/horarios', [HorarioController::class, 'store']);
Route::get('/horarios/{id}', [HorarioController::class, 'show']);
Route::put('/horarios/{id}', [HorarioController::class, 'update']);
Route::delete('/horarios/{id}', [HorarioController::class, 'destroy']);
