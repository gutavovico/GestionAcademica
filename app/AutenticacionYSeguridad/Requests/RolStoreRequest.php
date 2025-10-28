<?php

namespace App\AutenticacionYSeguridad\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:50', 'unique:roles,nombre'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}

