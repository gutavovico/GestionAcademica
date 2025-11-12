<?php

namespace App\ControlAsistencias\Services;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegistroAsistenciaService
{
    private const RETRASO_MIN = 10; // minutos tolerancia

    private function diaHoy(): string
    {
        $map = [
            1=>'Lunes', 2=>'Martes', 3=>'Miércoles', 4=>'Jueves', 5=>'Viernes', 6=>'Sábado', 0=>'Domingo'
        ];
        $dow = (int) Carbon::now()->locale('es')->dayOfWeek; // 0=Domingo .. 6=Sábado
        return $map[$dow] ?? 'Lunes';
    }

    public function misHorariosHoy(int $idUsuario): array
    {
        $dia = $this->diaHoy();
        $tz  = config('app.timezone', 'America/La_Paz');
        $hoy = Carbon::now($tz)->toDateString();

        $rows = DB::table('horario as h')
            ->leftJoin('carga_horaria as ch', 'ch.id_carga', '=', 'h.id_carga')
            ->leftJoin('materia as m', 'm.id_materia', '=', 'ch.id_materia')
            ->leftJoin('grupo as g', 'g.id_grupo', '=', 'ch.id_grupo')
            ->leftJoin('aula as a', 'a.id_aula', '=', 'h.id_aula')
            ->leftJoin('asistencia as asis', function($j) use ($hoy) {
                $j->on('asis.id_horario', '=', 'h.id_horario')
                  ->whereDate('asis.fecha_registro', $hoy);
            })
            ->where('h.estado', 'Activo')
            ->where('h.dia', $dia)
            ->where('ch.id_usuario', $idUsuario)
            ->select(
                'h.id_horario','h.hora_ini','h.hora_fin','h.dia','h.estado',
                'm.sigla','m.nombre as materia','g.nombre as grupo',
                'a.nroaula','a.id_modulo',
                'asis.id_asistencia','asis.tipo','asis.hora_registro','asis.metodo_registro as metodo','asis.observacion'
            )
            ->orderBy('h.hora_ini')
            ->get();

        $pendientes = [];
        $registrados = [];
        foreach ($rows as $r) {
            $item = [
                'id_horario' => (int)$r->id_horario,
                'hora_ini' => (string)$r->hora_ini,
                'hora_fin' => (string)$r->hora_fin,
                'sigla' => $r->sigla,
                'materia' => $r->materia,
                'grupo' => $r->grupo,
                'nroaula' => $r->nroaula,
                'id_modulo' => $r->id_modulo,
            ];
            if ($r->id_asistencia) {
                $item['tipo'] = $r->tipo;
                $item['hora_registro'] = $r->hora_registro;
                $item['metodo'] = $r->metodo;
                $item['observacion'] = $r->observacion;
                $registrados[] = $item;
            } else {
                $pendientes[] = $item;
            }
        }

        return ['dia' => $dia, 'pendientes' => $pendientes, 'registrados' => $registrados];
    }

    public function registrarParaDocente(int $idUsuario, int $idHorario, string $metodo = 'Manual', ?string $tipoOverride = null, ?string $observacion = null): array
    {
        $h = DB::table('horario as h')
            ->leftJoin('carga_horaria as ch', 'ch.id_carga', '=', 'h.id_carga')
            ->where('h.id_horario', $idHorario)
            ->where('h.estado', 'Activo')
            ->where('ch.id_usuario', $idUsuario)
            ->select('h.*')
            ->first();
        if (!$h) { return ['error' => 'Horario no válido para el docente']; }

        $diaHoy = $this->diaHoy();
        if ((string)$h->dia !== $diaHoy) { return ['error' => 'Fuera del día del horario']; }

        $hoy = Carbon::now()->toDateString();
        $ya = DB::table('asistencia')->where('id_horario', $idHorario)->whereDate('fecha_registro', $hoy)->exists();
        if ($ya) { return ['error' => 'Asistencia ya registrada hoy']; }

        $now = Carbon::now(); $ini = Carbon::createFromFormat('H:i:s', (string)$h->hora_ini);
        $fin = Carbon::createFromFormat('H:i:s', (string)$h->hora_fin);

        $tipo = null;
        $met = strtoupper($metodo) === 'QR' ? 'QR' : 'Manual';
        $allowed = ['Presente','Ausente','Retraso'];
        if ($tipoOverride && in_array($tipoOverride, $allowed, true)) {
            $tipo = $tipoOverride;
        } else {
            $limitePresente = (clone $ini)->addMinutes(self::RETRASO_MIN);
            if ($now->lessThanOrEqualTo($limitePresente)) $tipo = 'Presente';
            elseif ($now->lessThan($fin)) $tipo = 'Retraso';
            else $tipo = 'Ausente';
        }

        $id = DB::table('asistencia')->insertGetId([
            'id_horario' => (int)$idHorario,
            'fecha_registro' => $hoy,
            'hora_registro' => $now->format('H:i:s'),
            'tipo' => $tipo,
            'metodo_registro' => $met,
            'observacion' => $observacion,
        ], 'id_asistencia');

        $row = DB::table('asistencia')->where('id_asistencia', $id)->first();
        return ['ok' => 'Asistencia registrada', 'asistencia' => $row];
    }

    public function registrarComoAdmin(int $idHorario, string $tipo, string $metodo = 'Manual', ?string $observacion = null): array
    {
        $h = DB::table('horario')->where('id_horario', $idHorario)->where('estado','Activo')->first();
        if (!$h) { return ['error' => 'Horario no válido']; }
        $hoy = Carbon::now()->toDateString();
        $ya = DB::table('asistencia')->where('id_horario', $idHorario)->whereDate('fecha_registro', $hoy)->exists();
        if ($ya) { return ['error' => 'Asistencia ya registrada hoy']; }
        $allowed = ['Presente','Ausente','Retraso'];
        if (!in_array($tipo, $allowed, true)) { return ['error' => 'Tipo inválido']; }
        $id = DB::table('asistencia')->insertGetId([
            'id_horario' => (int)$idHorario,
            'fecha_registro' => $hoy,
            'hora_registro' => Carbon::now()->format('H:i:s'),
            'tipo' => $tipo,
            'metodo_registro' => strtoupper($metodo)==='QR'?'QR':'Manual',
            'observacion' => $observacion,
        ], 'id_asistencia');
        $row = DB::table('asistencia')->where('id_asistencia', $id)->first();
        return ['ok' => 'Asistencia registrada', 'asistencia' => $row];
    }

    public function horariosHoyAdmin(?string $dia = null, ?string $nombre = null, ?int $idUsuario = null): array
    {
        $d = $dia ?: $this->diaHoy();
        $tz  = config('app.timezone', 'America/La_Paz');
        $hoy = Carbon::now($tz)->toDateString();

        $q = DB::table('horario as h')
            ->leftJoin('carga_horaria as ch', 'ch.id_carga', '=', 'h.id_carga')
            ->leftJoin('usuario as u', 'u.id_usuario', '=', 'ch.id_usuario')
            ->leftJoin('materia as m', 'm.id_materia', '=', 'ch.id_materia')
            ->leftJoin('grupo as g', 'g.id_grupo', '=', 'ch.id_grupo')
            ->leftJoin('aula as a', 'a.id_aula', '=', 'h.id_aula')
            ->leftJoin('asistencia as asis', function($j) use ($hoy) {
                $j->on('asis.id_horario', '=', 'h.id_horario')
                  ->whereDate('asis.fecha_registro', $hoy);
            })
            ->where('h.estado', 'Activo')
            ->where('h.dia', $d)
            ->select(
                'h.id_horario','h.hora_ini','h.hora_fin','h.dia',
                'u.id_usuario','u.nombre as docente','u.correo',
                'm.sigla','m.nombre as materia','g.nombre as grupo',
                'a.nroaula','a.id_modulo',
                'asis.id_asistencia','asis.tipo','asis.hora_registro','asis.metodo_registro as metodo','asis.observacion'
            )
            ->orderBy('u.nombre')->orderBy('h.hora_ini');
        if ($idUsuario) { $q->where('u.id_usuario', $idUsuario); }
        if ($nombre && $nombre !== '') { $q->whereRaw('LOWER(u.nombre) LIKE ?', ['%'.strtolower($nombre).'%']); }
        $rows = $q->get();

        $pendientes = [];
        $registrados = [];
        foreach ($rows as $r) {
            $item = [
                'id_horario' => (int)$r->id_horario,
                'hora_ini' => (string)$r->hora_ini,
                'hora_fin' => (string)$r->hora_fin,
                'docente' => $r->docente,
                'sigla' => $r->sigla,
                'materia' => $r->materia,
                'grupo' => $r->grupo,
                'nroaula' => $r->nroaula,
                'id_modulo' => $r->id_modulo,
            ];
            if ($r->id_asistencia) {
                $item['tipo'] = $r->tipo;
                $item['hora_registro'] = $r->hora_registro;
                $item['metodo'] = $r->metodo;
                $item['observacion'] = $r->observacion;
                $registrados[] = $item;
            } else {
                $pendientes[] = $item;
            }
        }

        return ['dia' => $d, 'pendientes' => $pendientes, 'registrados' => $registrados];
    }
}
