<?php

namespace App\Administracion\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class PanelAdminService
{
    /**
     * Métricas generales para el panel administrativo.
     */
    public function metricas(): array
    {
        $usuariosCount = DB::table('usuario')
            ->where(function ($q) {
                $q->where('estado', true)->orWhereNull('estado');
            })
            ->count();

        $docentesCount = DB::table('usuario as u')
            ->join('roles as r', 'u.id_rol', '=', 'r.id_rol')
            ->where('r.nombre', 'Docente')
            ->where(function ($q) { $q->where('u.estado', true)->orWhereNull('u.estado'); })
            ->count();

        $administrativosCount = DB::table('usuario as u')
            ->join('roles as r', 'u.id_rol', '=', 'r.id_rol')
            ->whereIn('r.nombre', ['Administrador','Coordinador','Autoridad'])
            ->where(function ($q) { $q->where('u.estado', true)->orWhereNull('u.estado'); })
            ->count();

        $aulasCount = DB::table('aula')->count();
        $aulasDisponiblesCount = DB::table('aula')->where('disponible', true)->count();
        $horariosActivos = DB::table('horario')->where('estado', 'Activo')->count();

        $hoy = now()->toDateString();
        $asistenciasHoy = DB::table('asistencia')->whereDate('fecha_registro', $hoy)->count();

        return [
            'usuariosCount' => $usuariosCount,
            'docentesCount' => $docentesCount,
            'administrativosCount' => $administrativosCount,
            'aulasCount' => $aulasCount,
            'aulasDisponiblesCount' => $aulasDisponiblesCount,
            'horariosActivos' => $horariosActivos,
            'asistenciasHoy' => $asistenciasHoy,
        ];
    }
}

