<?php

namespace App\GestionAcademica\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MateriaStoreRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'sigla' => ['required', 'string', 'max:20', 'unique:materia,sigla'],
            'nombre' => ['required', 'string', 'max:100'],
            'semestre' => ['nullable', 'string', 'max:20'],
            'creditos' => ['nullable', 'integer'],
            'horas_semana' => ['nullable', 'integer'],
        ];
    }
}

