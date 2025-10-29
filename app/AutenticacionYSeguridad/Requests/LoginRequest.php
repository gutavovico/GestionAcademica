<?php

namespace App\AutenticacionYSeguridad\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determina si el usuario esto autorizado para hacer esta solicitud.
     */
    public function authorize(): bool
    {
        // Generalmente, se permite a todos los usuarios no autenticados acceder al login.
        return true;
    }

    /**
     * Obtiene las reglas de validacion que se aplican a la solicitud.
     */
    public function rules(): array
    {
        return [
            // Validamos que sea un correo electronico valido
            'correo' => ['required', 'string', 'email'],
            // Validamos que la contrasena sea requerida
            'contrasena' => ['required', 'string'],
        ];
    }

    /**
     * Mensajes de error personalizados para las reglas de validacion.
     */
    public function messages(): array
    {
        return [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'El formato del correo es invalido.',
            'contrasena.required' => 'La contrasena es obligatoria.',
        ];
    }
}

