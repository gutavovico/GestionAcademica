<?php

use Illuminate\Support\Facades\Route;

// Carga todas las rutas definidas en el archivo de autenticaci�n
require __DIR__.'/autenticacion.php';

// Define la ruta ra�z (opcional)
Route::get('/', function () {
    return view('welcome');
});
//HEAD
Route::get('/gestion-aulas/test', function () {
    return view('view');
});

Route::get('/gestion-aulas', function () {
    return view('gestion_aulas_horarios.aulas');
});

Route::get('/gestion-aulas/asignacion', function () {
    return view('gestion_aulas_horarios.asignacion');
});

Route::get('/gestion-aulas/horarios', function () {
    return view('gestion_aulas_horarios.horarios');
});

// Vista de prueba simple para login/logout
Route::get('/test', function () {
    return view('test');
});

//Autentificacion
