<?php

namespace App\AutenticacionYSeguridad\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'correo' => ['required', 'string', 'email', 'exists:usuario,correo'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'correo.required' => 'El correo es obligatorio.',
            'correo.email' => 'Ingrese un correo valido.',
            'correo.exists' => 'No encontramos un usuario con ese correo.',
            'password.required' => 'La nueva contrasena es obligatoria.',
            'password.min' => 'La contrasena debe contener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmacion de contrasena no coincide.',
        ];
    }
}

