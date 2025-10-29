<?php

namespace App\GestionAcademica\Services;

use App\GestionAcademica\Models\Grupo;

class GrupoService
{
    public function create(array $data): Grupo
    {
        return Grupo::create($data);
    }

    public function update(Grupo $grupo, array $data): Grupo
    {
        $grupo->update($data);
        return $grupo;
    }

    public function delete(Grupo $grupo): void
    {
        $grupo->delete();
    }
}

