<?php

namespace App\GestionAulasHorarios\Services;

use App\GestionAulasHorarios\Models\Aula;
use Illuminate\Support\Facades\DB;

class AulaService
{
    public function registrarAula(array $data, ?int $idUsuario = null): array
    {
        $modExiste = DB::table('modulo')
            ->where('id_modulo', $data['id_modulo'] ?? null)
            ->exists();
        if (!$modExiste) {
            return ['error' => 'Módulo inexistente'];
        }

        $duplicada = Aula::where('nroaula', $data['nroaula'] ?? null)
            ->where('id_modulo', $data['id_modulo'])
            ->exists();
        if ($duplicada) {
            return ['error' => 'Nro de aula ya registrado en ese módulo'];
        }

        if (!isset($data['capacidad']) || !is_numeric($data['capacidad']) || (int)$data['capacidad'] <= 0) {
            return ['error' => 'Capacidad inválida'];
        }

        return DB::transaction(function () use ($data, $idUsuario) {
            $aula = Aula::create([
                'nroaula'    => (int)$data['nroaula'],
                'tipo_aula'  => $data['tipo_aula'] ?? null,
                'capacidad'  => (int)$data['capacidad'],
                'disponible' => (bool)($data['disponible'] ?? true),
                'id_modulo'  => (int)$data['id_modulo'],
            ]);

            if ($idUsuario) {
                DB::table('bitacora')->insert([
                    'id_usuario' => $idUsuario,
                    'accion'     => 'Crear aula',
                    'detalle'    => "Aula {$aula->nroaula} en módulo {$aula->id_modulo}",
                    'ip'         => request()->ip(),
                ]);
            }
            return ['ok' => 'Aula creada', 'aula' => $aula];
        });
    }
}
