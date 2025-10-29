<?php

namespace App\Administracion\Services;

use App\Administracion\Models\Bitacora;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BitacoraService
{
    public function search(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $q = Bitacora::with(['usuario' => function ($q) {
            $q->select('id_usuario', 'nombre', 'correo');
        }])->orderByDesc('fecha')->orderByDesc('hora');

        if (!empty($filters['id_usuario'])) {
            $q->where('id_usuario', (int) $filters['id_usuario']);
        }
        if (!empty($filters['accion'])) {
            $q->where('accion', 'ilike', '%'.$filters['accion'].'%');
        }
        if (!empty($filters['ip'])) {
            $q->where('ip', 'ilike', '%'.$filters['ip'].'%');
        }
        if (!empty($filters['fecha_desde'])) {
            $q->where('fecha', '>=', $filters['fecha_desde']);
        }
        if (!empty($filters['fecha_hasta'])) {
            $q->where('fecha', '<=', $filters['fecha_hasta']);
        }

        return $q->paginate($perPage)->appends($filters);
    }
}

