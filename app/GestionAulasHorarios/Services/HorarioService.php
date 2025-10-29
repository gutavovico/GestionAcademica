<?php

namespace App\GestionAulasHorarios\Services;

use App\GestionAulasHorarios\Models\Horario;
use Illuminate\Support\Facades\DB;

class HorarioService
{
    public function listar(array $filtros = []): array
    {
        $q = DB::table('horario as h')
            ->leftJoin('aula as a', 'a.id_aula', '=', 'h.id_aula')
            ->leftJoin('carga_horaria as ch', 'ch.id_carga', '=', 'h.id_carga')
            ->select(
                'h.*',
                'a.nroaula', 'a.tipo_aula', 'a.capacidad', 'a.id_modulo',
                'ch.id_usuario', 'ch.id_materia', 'ch.id_grupo', 'ch.horas_semanales', 'ch.gestion'
            )
            ->orderBy('h.dia')
            ->orderBy('h.hora_ini');

        if (!empty($filtros['id_usuario'])) {
            $q->where('ch.id_usuario', (int)$filtros['id_usuario']);
        }
        if (!empty($filtros['dia'])) {
            $q->where('h.dia', $filtros['dia']);
        }
        if (!empty($filtros['estado'])) {
            $q->where('h.estado', $filtros['estado']);
        }

        return ['items' => $q->get()->toArray()];
    }

    public function obtener(int $id): array
    {
        $h = DB::table('horario')->where('id_horario', $id)->first();
        if (!$h) return ['error' => 'Horario no encontrado'];
        return ['horario' => $h];
    }

    public function crear(array $data): array
    {
        // Validaciones de negocio: aula existente y sin solape activo
        $aula = DB::table('aula')->where('id_aula', $data['id_aula'])->first();
        if (!$aula) return ['error' => 'Aula no encontrada'];
        if (!(bool)$aula->disponible) return ['error' => 'Aula no disponible'];

        $solape = DB::table('horario')
            ->where('id_aula', $data['id_aula'])
            ->where('dia', $data['dia'])
            ->where('estado', 'Activo')
            ->where('hora_fin', '>', $data['hora_ini'])
            ->where('hora_ini', '<', $data['hora_fin'])
            ->exists();
        if ($solape) return ['error' => 'Conflicto con otra reserva'];

        $id = DB::table('horario')->insertGetId([
            'id_carga' => $data['id_carga'] ?? null,
            'id_aula'  => (int)$data['id_aula'],
            'dia'      => $data['dia'],
            'hora_ini' => $data['hora_ini'],
            'hora_fin' => $data['hora_fin'],
            'estado'   => $data['estado'] ?? 'Activo',
        ], 'id_horario');

        return $this->obtener($id);
    }

    public function actualizar(int $id, array $data): array
    {
        $h = DB::table('horario')->where('id_horario', $id)->first();
        if (!$h) return ['error' => 'Horario no encontrado'];

        if (isset($data['id_aula'])) {
            $aula = DB::table('aula')->where('id_aula', $data['id_aula'])->first();
            if (!$aula) return ['error' => 'Aula no encontrada'];
            if (!(bool)$aula->disponible) return ['error' => 'Aula no disponible'];
        }

        $idAula = $data['id_aula'] ?? $h->id_aula;
        $dia    = $data['dia'] ?? $h->dia;
        $ini    = $data['hora_ini'] ?? $h->hora_ini;
        $fin    = $data['hora_fin'] ?? $h->hora_fin;

        $solape = DB::table('horario')
            ->where('id_aula', $idAula)
            ->where('dia', $dia)
            ->where('estado', 'Activo')
            ->where('id_horario', '!=', $id)
            ->where('hora_fin', '>', $ini)
            ->where('hora_ini', '<', $fin)
            ->exists();
        if ($solape) return ['error' => 'Conflicto con otra reserva'];

        DB::table('horario')->where('id_horario', $id)->update([
            'id_carga' => array_key_exists('id_carga', $data) ? $data['id_carga'] : $h->id_carga,
            'id_aula'  => $idAula,
            'dia'      => $dia,
            'hora_ini' => $ini,
            'hora_fin' => $fin,
            'estado'   => $data['estado'] ?? $h->estado,
        ]);

        return $this->obtener($id);
    }

    // Destroy lógico: marcar estado='Activo' (según requerimiento)
    public function marcarAsignado(int $id): array
    {
        $h = DB::table('horario')->where('id_horario', $id)->first();
        if (!$h) return ['error' => 'Horario no encontrado'];
        DB::table('horario')->where('id_horario', $id)->update(['estado' => 'Activo']);
        return $this->obtener($id);
    }
}

