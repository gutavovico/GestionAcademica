<?php

namespace App\GestionAcademica\Services;

use App\GestionAcademica\Models\CargaHoraria;

class CargaHorariaService
{
    public function assign(array $data): CargaHoraria
    {
        return CargaHoraria::create($data);
    }

    public function delete(CargaHoraria $carga): void
    {
        $carga->delete();
    }
}

