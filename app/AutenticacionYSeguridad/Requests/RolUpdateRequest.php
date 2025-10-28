<?php

namespace App\AutenticacionYSeguridad\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RolUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = (int) ($this->route('id') ?? $this->route('rol'));
        return [
            'nombre' => ['required', 'string', 'max:50', 'unique:roles,nombre,'.$id.',id_rol'],
            'descripcion' => ['nullable', 'string'],
        ];
    }
}

