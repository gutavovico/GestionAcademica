<?php

namespace App\ControlAsistencias\Services;

use Illuminate\Support\Facades\DB;

class VerHorarioSemanalService
{
    /**
     * Obtiene el horario semanal agrupado por día para un docente.
     * Solo considera horarios con estado 'Activo'.
     */
    public function paraDocente(int $idUsuario): array
    {
        $diasOrden = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];

        // Subconsulta: última asistencia por horario (por id_asistencia más alto)
        $ultAsist = DB::table('asistencia as x')
            ->select('x.id_horario', DB::raw('MAX(x.id_asistencia) as id_asistencia'))
            ->groupBy('x.id_horario');

        $rows = DB::table('horario as h')
            ->leftJoin('carga_horaria as ch', 'ch.id_carga', '=', 'h.id_carga')
            ->leftJoin('materia as m', 'm.id_materia', '=', 'ch.id_materia')
            ->leftJoin('grupo as g', 'g.id_grupo', '=', 'ch.id_grupo')
            ->leftJoin('aula as a', 'a.id_aula', '=', 'h.id_aula')
            ->leftJoin('modulo as mo', 'mo.id_modulo', '=', 'a.id_modulo')
            ->leftJoinSub($ultAsist, 'ua', function ($join) {
                $join->on('ua.id_horario', '=', 'h.id_horario');
            })
            ->leftJoin('asistencia as at', 'at.id_asistencia', '=', 'ua.id_asistencia')
            ->where('ch.id_usuario', $idUsuario)
            ->where('h.estado', 'Activo')
            ->orderByRaw("CASE 
                WHEN lower(h.dia) LIKE 'lunes%' THEN 1
                WHEN lower(h.dia) LIKE 'martes%' THEN 2
                WHEN lower(h.dia) LIKE 'mi%coles%' THEN 3
                WHEN lower(h.dia) LIKE 'jueves%' THEN 4
                WHEN lower(h.dia) LIKE 'viernes%' THEN 5
                WHEN lower(h.dia) LIKE 's%bado%' THEN 6
                WHEN lower(h.dia) LIKE 'domingo%' THEN 7
                ELSE 8 END")
            ->orderBy('h.hora_ini')
            ->select(
                'h.id_horario','h.dia','h.hora_ini','h.hora_fin','h.estado',
                'a.id_aula','a.nroaula','a.tipo_aula','a.capacidad','a.id_modulo',
                DB::raw("COALESCE(m.sigla, '') as materia_sigla"),
                DB::raw("COALESCE(m.nombre, '') as materia_nombre"),
                DB::raw("COALESCE(g.nombre, '') as grupo"),
                DB::raw("COALESCE(mo.facultad, '') as facultad"),
                DB::raw("COALESCE(at.tipo, 'no registrada') as asistencia_tipo"),
                DB::raw("at.fecha_registro"),
                DB::raw("at.hora_registro")
            )
            ->get();

        // Inicializar mapa de días
        $map = [];
        foreach ($diasOrden as $d) {
            $map[$d] = [];
        }

        foreach ($rows as $r) {
            $dia = (string) $r->dia;
            if (!array_key_exists($dia, $map)) {
                $map[$dia] = [];
            }
            $map[$dia][] = [
                'id_horario' => (int) $r->id_horario,
                'hora_ini' => (string) $r->hora_ini,
                'hora_fin' => (string) $r->hora_fin,
                'materia' => trim(((string) $r->materia_sigla) . ' ' . ((string) $r->materia_nombre)),
                'grupo' => (string) $r->grupo,
                'aula' => $r->nroaula !== null ? ('Aula ' . $r->nroaula) : '',
                'modulo' => $r->id_modulo !== null ? (int) $r->id_modulo : null,
                'tipo_aula' => (string) ($r->tipo_aula ?? ''),
                'asistencia' => (string) ($r->asistencia_tipo ?? 'no registrada'),
                'asistencia_fecha' => isset($r->fecha_registro) ? (string) $r->fecha_registro : null,
                'asistencia_hora' => isset($r->hora_registro) ? (string) $r->hora_registro : null,
            ];
        }

        $count = 0;
        foreach ($map as $items) { $count += count($items); }

        return [
            'dias' => $map,
            'count' => $count,
        ];
    }
}

