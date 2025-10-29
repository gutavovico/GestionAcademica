<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

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
