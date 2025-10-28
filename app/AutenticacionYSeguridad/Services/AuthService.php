<?php

namespace App\AutenticacionYSeguridad\Services;

use App\AutenticacionYSeguridad\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    /**
     * Intenta autenticar con correo y contraseña.
     * - Si la contraseña en BD es Bcrypt, verifica y autentica.
     * - Si está en texto plano (datos iniciales), la migra a Bcrypt y autentica.
     */
    public function attemptLogin(string $correo, string $contrasena): bool
    {
        // Obtener usuario por correo
        $usuario = Usuario::where('correo', $correo)->first();
        if (!$usuario) {
            return false;
        }

        $stored = (string) $usuario->contrasena;
        $isBcrypt = $stored !== '' && str_starts_with($stored, '$2y$');

        if ($isBcrypt) {
            // Validar con Bcrypt y autenticar
            if (Hash::check($contrasena, $stored)) {
                Auth::login($usuario);
                return true;
            }
            return false;
        }

        // Caso: contraseñas en texto plano de la carga inicial
        if ($stored === $contrasena) {
            $usuario->contrasena = Hash::make($contrasena);
            $usuario->save();
            Auth::login($usuario);
            return true;
        }

        return false;
    }

    /**
     * Cierra la sesión del usuario actual.
     */
    public function logout(): void
    {
        Auth::logout();
    }
}
