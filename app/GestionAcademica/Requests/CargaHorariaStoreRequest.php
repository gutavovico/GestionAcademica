<?php

namespace App\GestionAcademica\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\GestionAcademica\Models\CargaHoraria;

class CargaHorariaStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_usuario' => ['required', 'integer', 'exists:usuario,id_usuario'],
            'id_materia' => ['required', 'integer', 'exists:materia,id_materia'],
            'id_grupo' => ['required', 'integer', 'exists:grupo,id_grupo'],
            'horas_semanales' => ['nullable', 'integer'],
            'gestion' => ['required', 'string', 'max:20'],
            'estado' => ['nullable', 'string', 'max:20'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $exists = CargaHoraria::where('id_materia', (int) $this->input('id_materia'))
                ->where('id_grupo', (int) $this->input('id_grupo'))
                ->where('gestion', (string) $this->input('gestion'))
                ->exists();

            if ($exists) {
                $v->errors()->add('id_materia', 'Ya existe una carga para esta materia y grupo en la gestión indicada.');
            }
        });
    }
}
