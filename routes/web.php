<?php

use Illuminate\Support\Facades\Route;

// Carga todas las rutas definidas en el archivo de autenticación
require __DIR__.'/autenticacion.php';

// Define la ruta raíz (opcional)
Route::get('/', function () {
    return view('welcome');
});

// Vista de prueba simple para login/logout
Route::get('/test', function () {
    return view('test');
});

