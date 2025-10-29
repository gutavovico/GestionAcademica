<?php

namespace App\GestionAcademica\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GrupoUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = (int) ($this->route('id') ?? $this->route('grupo'));
        return [
            'nombre' => ['required', 'string', 'max:2', 'unique:grupo,nombre,'.$id.',id_grupo'],
            'turno' => ['nullable', 'string', 'max:20'],
            'capacidad_max' => ['nullable', 'integer'],
        ];
    }
}

