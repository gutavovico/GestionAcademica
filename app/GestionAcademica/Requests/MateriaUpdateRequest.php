<?php

namespace App\GestionAcademica\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MateriaUpdateRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $id = (int) ($this->route('id') ?? $this->route('materia'));
        return [
            'sigla' => ['required', 'string', 'max:20', 'unique:materia,sigla,'.$id.',id_materia'],
            'nombre' => ['required', 'string', 'max:100'],
            'semestre' => ['nullable', 'string', 'max:20'],
            'creditos' => ['nullable', 'integer'],
            'horas_semana' => ['nullable', 'integer'],
        ];
    }
}

