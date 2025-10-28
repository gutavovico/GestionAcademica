<?php

namespace App\AutenticacionYSeguridad\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UsuarioUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (int) ($this->route('id') ?? $this->route('usuario'));

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'correo' => ['required', 'string', 'email', 'max:100', 'unique:usuario,correo,'.$id.',id_usuario'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'contrasena' => ['nullable', 'string', 'min:8'],
            'id_rol' => ['required', 'integer', 'exists:roles,id_rol'],
            'estado' => ['nullable', 'boolean'],
        ];
    }
}

