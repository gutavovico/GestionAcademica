<?php

namespace App\GestionAcademica\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrupoStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:2', 'unique:grupo,nombre'],
            'turno' => ['nullable', 'string', 'max:20'],
            'capacidad_max' => ['nullable', 'integer'],
        ];
    }
}

