<?php

namespace App\Administracion\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BitacoraFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_usuario' => ['nullable', 'integer'],
            'accion' => ['nullable', 'string'],
            'ip' => ['nullable', 'string'],
            'fecha_desde' => ['nullable', 'date'],
            'fecha_hasta' => ['nullable', 'date', 'after_or_equal:fecha_desde'],
        ];
    }
}

