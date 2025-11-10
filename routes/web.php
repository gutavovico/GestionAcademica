<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Carga todas las rutas definidas en el archivo de autenticaci�n
require __DIR__.'/autenticacion.php';

// Define la ruta ra�z (opcional)
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
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

Route::get('/gestion-aulas/registrar', function () {
    return view('gestion_aulas_horarios.registrar');
});

Route::get('/gestion-aulas/registrar-aula-horario', function () {
    return view('gestion_aulas_horarios.registrar_aula_horario');
});

// Vista de prueba simple para login/logout
Route::get('/test', function () {
    return view('test');
});

//Autentificacion
