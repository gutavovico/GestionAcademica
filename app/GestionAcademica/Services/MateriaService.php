<?php

namespace App\GestionAcademica\Services;

use App\GestionAcademica\Models\Materia;

class MateriaService
{
    public function create(array $data): Materia
    {
        return Materia::create($data);
    }

    public function update(Materia $materia, array $data): Materia
    {
        $materia->update($data);
        return $materia;
    }

    public function delete(Materia $materia): void
    {
        $materia->delete();
    }
}

