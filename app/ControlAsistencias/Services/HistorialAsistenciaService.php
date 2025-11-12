<?php

namespace App\ControlAsistencias\Services;

use Illuminate\Support\Facades\DB;

class HistorialAsistenciaService
{
    /**
     * Busca registros de asistencia con filtros.
     * Filtros soportados: fecha_desde, fecha_hasta, id_materia, gestion, tipo, metodo, texto.
     * Si se pasa $idUsuario limita a ese docente.
     */
    public function buscar(array $filtros = [], ?int $idUsuario = null): array
    {
        $q = DB::table('asistencia as a')
            ->join('horario as h', 'h.id_horario', '=', 'a.id_horario')
            ->leftJoin('carga_horaria as ch', 'ch.id_carga', '=', 'h.id_carga')
            ->leftJoin('materia as m', 'm.id_materia', '=', 'ch.id_materia')
            ->leftJoin('grupo as g', 'g.id_grupo', '=', 'ch.id_grupo')
            ->leftJoin('aula as au', 'au.id_aula', '=', 'h.id_aula')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'ch.id_usuario')
            ->when($idUsuario, fn ($qq) => $qq->where('ch.id_usuario', (int) $idUsuario))
            ->when(!empty($filtros['fecha_desde']), fn ($qq) => $qq->where('a.fecha_registro', '>=', $filtros['fecha_desde']))
            ->when(!empty($filtros['fecha_hasta']), fn ($qq) => $qq->where('a.fecha_registro', '<=', $filtros['fecha_hasta']))
            ->when(!empty($filtros['id_materia']), fn ($qq) => $qq->where('m.id_materia', (int) $filtros['id_materia']))
            ->when(!empty($filtros['gestion']), fn ($qq) => $qq->where('ch.gestion', $filtros['gestion']))
            ->when(!empty($filtros['tipo']), fn ($qq) => $qq->where('a.tipo', $filtros['tipo']))
            ->when(!empty($filtros['metodo']), fn ($qq) => $qq->where('a.metodo_registro', $filtros['metodo']))
            ->when(!empty($filtros['texto']), function ($qq) use ($filtros) {
                $t = '%' . str_replace('%', '', (string) $filtros['texto']) . '%';
                $qq->where(function ($w) use ($t) {
                    $w->where('a.observacion', 'ilike', $t)
                      ->orWhere('m.nombre', 'ilike', $t)
                      ->orWhere('m.sigla', 'ilike', $t)
                      ->orWhere('u.nombre', 'ilike', $t);
                });
            })
            ->orderByDesc('a.fecha_registro')
            ->orderByDesc('a.hora_registro')
            ->select(
                'a.id_asistencia','a.id_horario','a.fecha_registro','a.hora_registro',
                'a.tipo','a.metodo_registro','a.observacion',
                'h.dia','h.hora_ini','h.hora_fin',
                'm.id_materia','m.sigla','m.nombre as materia',
                'g.nombre as grupo','ch.gestion',
                'u.id_usuario as id_docente','u.nombre as docente',
                'au.nroaula','au.id_modulo'
            )
            ->limit(300)
            ->get();

        return ['items' => $rows = $q->toArray(), 'count' => count($rows)];
    }
}

