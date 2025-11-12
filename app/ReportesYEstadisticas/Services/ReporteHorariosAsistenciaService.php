<?php

namespace App\ReportesYEstadisticas\Services;

use Illuminate\Support\Facades\DB;

class ReporteHorariosAsistenciaService
{
    public function opciones(): array
    {
        $docentes = DB::table('usuario as u')
            ->join('roles as r','r.id_rol','=','u.id_rol')
            ->where('r.nombre','Docente')
            ->orderBy('u.nombre')
            ->select('u.id_usuario','u.nombre')
            ->get()->toArray();
        $materias = DB::table('materia')->orderBy('nombre')->select('id_materia','sigla','nombre')->get()->toArray();
        $grupos = DB::table('grupo')->orderBy('nombre')->select('id_grupo','nombre')->get()->toArray();
        $gestiones = DB::table('carga_horaria')->whereNotNull('gestion')->distinct()->orderByDesc('gestion')->pluck('gestion')->toArray();
        return compact('docentes','materias','grupos','gestiones');
    }

    public function data(string $desde, string $hasta, ?int $idUsuario = null, ?int $idMateria = null, ?int $idGrupo = null, ?string $gestion = null): array
    {
        $q = DB::table('asistencia as a')
            ->join('horario as h', 'h.id_horario', '=', 'a.id_horario')
            ->leftJoin('carga_horaria as ch', 'ch.id_carga', '=', 'h.id_carga')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'ch.id_usuario')
            ->leftJoin('materia as m', 'm.id_materia', '=', 'ch.id_materia')
            ->leftJoin('grupo as g', 'g.id_grupo', '=', 'ch.id_grupo')
            ->leftJoin('aula as au', 'au.id_aula', '=', 'h.id_aula')
            ->whereBetween('a.fecha_registro', [$desde, $hasta]);

        if ($idUsuario) { $q->where('u.id_usuario', $idUsuario); }
        if ($idMateria) { $q->where('m.id_materia', $idMateria); }
        if ($idGrupo)   { $q->where('g.id_grupo', $idGrupo); }
        if ($gestion)   { $q->where('ch.gestion', $gestion); }

        $detalle = (clone $q)
            ->select(
                'a.id_asistencia','a.fecha_registro','a.hora_registro','a.tipo',
                'u.nombre as docente','m.sigla','m.nombre as materia','g.nombre as grupo',
                'h.dia','h.hora_ini','h.hora_fin','au.nroaula','au.id_modulo'
            )
            ->orderBy('a.fecha_registro')->orderBy('a.hora_registro')
            ->get()->toArray();

        $porDocente = (clone $q)
            ->select('u.id_usuario','u.nombre',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN a.tipo='Presente' THEN 1 ELSE 0 END) as presentes"),
                DB::raw("SUM(CASE WHEN a.tipo='Ausente' THEN 1 ELSE 0 END) as ausentes"),
                DB::raw("SUM(CASE WHEN a.tipo='Retraso' THEN 1 ELSE 0 END) as retrasos")
            )
            ->groupBy('u.id_usuario','u.nombre')
            ->orderByDesc('total')
            ->get()->toArray();

        $porMateria = (clone $q)
            ->select('m.id_materia','m.sigla','m.nombre',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN a.tipo='Presente' THEN 1 ELSE 0 END) as presentes"),
                DB::raw("SUM(CASE WHEN a.tipo='Ausente' THEN 1 ELSE 0 END) as ausentes"),
                DB::raw("SUM(CASE WHEN a.tipo='Retraso' THEN 1 ELSE 0 END) as retrasos")
            )
            ->groupBy('m.id_materia','m.sigla','m.nombre')
            ->orderByDesc('total')
            ->get()->toArray();

        return [
            'filtros' => compact('desde','hasta','idUsuario','idMateria','idGrupo','gestion'),
            'resumen' => [ 'por_docente' => $porDocente, 'por_materia' => $porMateria ],
            'detalle' => $detalle,
        ];
    }
}

