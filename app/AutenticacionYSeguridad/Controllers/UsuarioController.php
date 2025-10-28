<?php

namespace App\AutenticacionYSeguridad\Controllers;

use App\AutenticacionYSeguridad\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;

class UsuarioController extends Controller
{
    /**
     * Registra un nuevo usuario y hashea la contraseña.
     */
    public function store(Request $request)
    {
        // 1. Validar datos
        $request->validate([
            'nombre' => 'required|string|max:100',
            'correo' => 'required|string|email|unique:usuario,correo',
            'contrasena' => 'required|string|min:8',
            'id_rol' => 'required|integer|exists:roles,id_rol',
        ]);

        // 2. CREACIÓN DEL HASH DE LARAVEL (CRUCIAL)
        $hashedPassword = Hash::make($request->contrasena);

        // 3. Crear el usuario en la base de datos
        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'correo' => $request->correo,
            'contrasena' => $hashedPassword, // Se guarda la versión hasheada
            'telefono' => $request->telefono ?? null,
            'id_rol' => $request->id_rol,
        ]);

        return response()->json(['message' => 'Usuario registrado exitosamente.', 'user' => $usuario], 201);
    }
}
