<?php

namespace App\AutenticacionYSeguridad\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'string', 'email', 'max:100', 'unique:usuario,correo'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'contrasena' => ['required', 'string', 'min:8'],
            'id_rol' => ['required', 'integer', 'exists:roles,id_rol'],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}

