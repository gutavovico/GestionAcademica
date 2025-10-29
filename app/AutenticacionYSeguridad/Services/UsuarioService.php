<?php

namespace App\AutenticacionYSeguridad\Services;

use App\AutenticacionYSeguridad\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class UsuarioService
{
    public function create(array $data): Usuario
    {
        $payload = [
            'nombre' => $data['nombre'],
            'correo' => $data['correo'],
            'telefono' => $data['telefono'] ?? null,
            'id_rol' => $data['id_rol'],
            'contrasena' => Hash::make($data['contrasena']),
        ];
        if (array_key_exists('estado', $data)) {
            $payload['estado'] = (bool) $data['estado'];
        }
        return Usuario::create($payload);
    }

    public function update(Usuario $usuario, array $data): Usuario
    {
        $usuario->nombre = $data['nombre'] ?? $usuario->nombre;
        $usuario->correo = $data['correo'] ?? $usuario->correo;
        $usuario->telefono = $data['telefono'] ?? $usuario->telefono;
        if (isset($data['id_rol'])) {
            $usuario->id_rol = $data['id_rol'];
        }
        if (!empty($data['contrasena'])) {
            $usuario->contrasena = Hash::make($data['contrasena']);
        }
        if (array_key_exists('estado', $data)) {
            $usuario->estado = (bool) $data['estado'];
        }
        $usuario->save();
        return $usuario;
    }

    public function toggleEstado(Usuario $usuario): Usuario
    {
        $usuario->estado = ! (bool) ($usuario->estado ?? true);
        $usuario->save();
        return $usuario;
    }
}

