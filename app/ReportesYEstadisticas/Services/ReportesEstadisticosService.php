<?php

namespace App\ReportesYEstadisticas\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class ReportesEstadisticosService
{
    public function opciones(): array
    {
        $gestiones = DB::table('carga_horaria')
            ->select('gestion')
            ->whereNotNull('gestion')
            ->distinct()->orderByDesc('gestion')->get()->pluck('gestion')->toArray();
        $modulos = DB::table('modulo')->select('id_modulo','facultad')->orderBy('id_modulo')->get()->toArray();
        return [ 'gestiones' => $gestiones, 'modulos' => $modulos ];
    }

    public function data(?string $gestion = null, ?int $idModulo = null): array
    {
        $aulasQuery = DB::table('aula');
        if ($idModulo !== null) { $aulasQuery->where('id_modulo', $idModulo); }
        $totalAulas = (clone $aulasQuery)->count();
        $aulasDisponibles = (clone $aulasQuery)->where('disponible', true)->count();

        $horarios = DB::table('horario as h')
            ->join('aula as a', 'a.id_aula', '=', 'h.id_aula')
            ->leftJoin('carga_horaria as c', 'c.id_carga', '=', 'h.id_carga')
            ->where('h.estado', 'Activo');
        if ($idModulo !== null) { $horarios->where('a.id_modulo', $idModulo); }
        if ($gestion !== null && $gestion !== '') { $horarios->where('c.gestion', $gestion); }

        $horariosActivos = (clone $horarios)->count();
        $porDia = (clone $horarios)
            ->select('h.dia', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('h.dia')
            ->orderByRaw("CASE h.dia WHEN 'Lunes' THEN 1 WHEN 'Martes' THEN 2 WHEN 'Miércoles' THEN 3 WHEN 'Miercoles' THEN 3 WHEN 'Jueves' THEN 4 WHEN 'Viernes' THEN 5 WHEN 'Sábado' THEN 6 WHEN 'Sabado' THEN 6 ELSE 7 END")
            ->get()->toArray();
        $aulasOcupadas = (clone $horarios)->distinct('h.id_aula')->count('h.id_aula');

        $cargas = DB::table('carga_horaria as ch')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'ch.id_usuario')
            ->when($gestion, fn($q) => $q->where('ch.gestion', $gestion));
        $totalCargas = (clone $cargas)->count();
        $horasTotales = (clone $cargas)->sum('ch.horas_semanales');
        $docentesConCarga = (clone $cargas)->distinct('ch.id_usuario')->count('ch.id_usuario');
        $topDocentes = (clone $cargas)
            ->select('u.nombre', DB::raw('SUM(ch.horas_semanales) as horas'))
            ->groupBy('u.nombre')
            ->orderByDesc('horas')
            ->limit(5)
            ->get()->toArray();

        // Docentes totales (independiente de cargas)
        $docentesTotal = DB::table('usuario as u')
            ->join('roles as r', 'u.id_rol', '=', 'r.id_rol')
            ->where('r.nombre', 'Docente')
            ->where(function($q){ $q->where('u.estado', true)->orWhereNull('u.estado'); })
            ->count();

        $asis = DB::table('asistencia as a')
            ->join('horario as h', 'h.id_horario', '=', 'a.id_horario')
            ->leftJoin('carga_horaria as ch', 'ch.id_carga', '=', 'h.id_carga')
            ->leftJoin('aula as au', 'au.id_aula', '=', 'h.id_aula');
        if ($gestion) { $asis->where('ch.gestion', $gestion); }
        if ($idModulo !== null) { $asis->where('au.id_modulo', $idModulo); }
        $porTipo = (clone $asis)
            ->select('a.tipo', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('a.tipo')->get()->toArray();

        // Resumen de hoy
        $hoy = Carbon::now()->toDateString();
        $hoyTipos = (clone $asis)
            ->select('a.tipo', DB::raw('COUNT(*) as cantidad'))
            ->whereDate('a.fecha_registro', $hoy)
            ->groupBy('a.tipo')->pluck('cantidad','tipo')->toArray();
        $presentesHoy = (int)($hoyTipos['Presente'] ?? 0);
        $ausentesHoy  = (int)($hoyTipos['Ausente'] ?? 0);
        $retrasosHoy  = (int)($hoyTipos['Retraso'] ?? 0);

        // Tendencia últimos 14 días
        $desde = Carbon::now()->subDays(13)->toDateString();
        $serie = (clone $asis)
            ->select(DB::raw('DATE(a.fecha_registro) as fecha'), 'a.tipo', DB::raw('COUNT(*) as cantidad'))
            ->whereDate('a.fecha_registro', '>=', $desde)
            ->groupBy(DB::raw('DATE(a.fecha_registro)'), 'a.tipo')
            ->orderBy(DB::raw('DATE(a.fecha_registro)'))
            ->get()->toArray();
        // Normalizar a arreglo por día
        $map = [];
        foreach ($serie as $row) {
            $f = (string)($row->fecha ?? $row['fecha'] ?? '');
            $t = (string)($row->tipo ?? $row['tipo'] ?? '');
            $c = (int)($row->cantidad ?? $row['cantidad'] ?? 0);
            $map[$f] = $map[$f] ?? ['fecha'=>$f,'Presente'=>0,'Ausente'=>0,'Retraso'=>0];
            if(isset($map[$f][$t])){ $map[$f][$t] += $c; }
        }
        // Completar días faltantes
        $tendencia = [];
        for($i=0;$i<14;$i++){
            $f = Carbon::now()->subDays(13-$i)->toDateString();
            $tendencia[] = array_merge(['fecha'=>$f,'Presente'=>0,'Ausente'=>0,'Retraso'=>0], $map[$f] ?? []);
        }
        // Tasa últimos 30 días
        $desde30 = Carbon::now()->subDays(29)->toDateString();
        $ult30 = (clone $asis)
            ->select('a.tipo', DB::raw('COUNT(*) as cantidad'))
            ->whereDate('a.fecha_registro', '>=', $desde30)
            ->groupBy('a.tipo')->pluck('cantidad','tipo')->toArray();
        $p30 = (int)($ult30['Presente'] ?? 0); $a30 = (int)($ult30['Ausente'] ?? 0);
        $tasa30 = ($p30 + $a30) > 0 ? round(($p30/($p30+$a30))*100, 1) : null;

        return [
            'filtros' => [ 'gestion' => $gestion, 'id_modulo' => $idModulo ],
            'resumen' => [
                'docentes_total' => $docentesTotal,
                'presentes_hoy'  => $presentesHoy,
                'ausentes_hoy'   => $ausentesHoy,
                'retrasos_hoy'   => $retrasosHoy,
                'tasa_asistencia_30d' => $tasa30,
            ],
            'aulas' => [
                'total' => $totalAulas,
                'disponibles' => $aulasDisponibles,
                'ocupadas_distintas' => $aulasOcupadas,
                'horarios_activos' => $horariosActivos,
                'por_dia' => $porDia,
            ],
            'carga_docente' => [
                'docentes_con_carga' => $docentesConCarga,
                'cargas' => $totalCargas,
                'horas_totales' => (int) $horasTotales,
                'top_docentes' => $topDocentes,
            ],
            'asistencia' => [
                'por_tipo' => $porTipo,
                'tendencia' => $tendencia,
            ],
        ];
    }
}
