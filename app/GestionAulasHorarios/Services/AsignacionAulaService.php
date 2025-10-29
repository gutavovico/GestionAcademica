<?php

namespace App\GestionAulasHorarios\Services;

use App\GestionAulasHorarios\Models\Aula;
use Illuminate\Support\Facades\DB;

class AsignacionAulaService
{
    public function listarModulos(): array
    {
        $mods = DB::table('modulo')->select('id_modulo','facultad')->orderBy('id_modulo')->get()->toArray();
        return ['modulos' => $mods];
    }
    /**
     * Lista aulas activas/disponibles con filtros simples.
     */
    public function listarAulas(?int $capacidadMin = null, ?int $idModulo = null, ?string $tipoAula = null): array
    {
        $q = DB::table('aula')
            ->where('disponible', true);

        if ($capacidadMin !== null) {
            $q->where('capacidad', '>=', $capacidadMin);
        }
        if ($idModulo !== null) {
            $q->where('id_modulo', $idModulo);
        }
        if ($tipoAula !== null && $tipoAula !== '') {
            $q->where('tipo_aula', $tipoAula);
        }

        $aulas = $q->orderBy('nroaula')->get()->toArray();
        return ['aulas' => $aulas];
    }

    /**
     * Devuelve slots de 90 minutos (07:00–22:30) con marca de ocupado/libre
     * para un aula y un día. Un slot está ocupado si se solapa con algún
     * registro en `horario` con estado='Activo'.
     */
    public function slotsDisponibles(int $idAula, string $dia): array
    {
        // Horarios ocupados (Activos) del aula en ese día
        $ocupados = DB::table('horario')
            ->where('id_aula', $idAula)
            ->where('dia', $dia)
            ->where('estado', 'Activo')
            ->select('hora_ini', 'hora_fin')
            ->get()
            ->map(fn($h) => ['ini' => (string)$h->hora_ini, 'fin' => (string)$h->hora_fin])
            ->toArray();

        // Generar slots
        $slots = [
            ['07:00','08:30'],
            ['08:30','10:00'],
            ['10:00','11:30'],
            ['11:30','13:00'],
            ['13:00','14:30'],
            ['14:30','16:00'],
            ['16:00','17:30'],
            ['17:30','19:00'],
            ['19:00','20:30'],
            ['20:30','22:00'],
            ['21:00','22:30'],
        ];

        // Helpers de tiempo (minutos)
        $toMin = function (string $t): int {
            $p = explode(':', $t);
            $h = (int)($p[0] ?? 0); $m = (int)($p[1] ?? 0);
            return $h * 60 + $m;
        };
        $overlaps = function (string $aIni, string $aFin, string $bIni, string $bFin) use ($toMin): bool {
            $ai = $toMin($aIni); $af = $toMin($aFin);
            $bi = $toMin($bIni); $bf = $toMin($bFin);
            return ($af > $bi) && ($ai < $bf);
        };

        $result = [];
        $libres  = [];
        foreach ($slots as [$ini, $fin]) {
            $ocupado = false;
            foreach ($ocupados as $o) {
                if ($overlaps($ini, $fin, $o['ini'], $o['fin'])) {
                    $ocupado = true; break;
                }
            }
            $item = ['hora_ini' => $ini, 'hora_fin' => $fin, 'ocupado' => $ocupado];
            $result[] = $item;
            if (!$ocupado) { $libres[] = $item; }
        }

        return ['slots' => $result, 'libres' => $libres];
    }
    /**
     * Lista aulas disponibles para un día y rango horario dados,
     * filtrando por capacidad mínima y filtros opcionales.
     * Un aula NO está disponible si existe un horario Activo para ese día
     * cuyo rango de tiempo se solape con [hora_ini, hora_fin).
     */
    public function listarAulasDisponibles(string $dia, string $horaIni, string $horaFin, int $capacidadMin = 1, ?int $idModulo = null, ?string $tipoAula = null): array
    {
        $query = DB::table('aula as a')
            ->where('a.disponible', true)
            ->where('a.capacidad', '>=', $capacidadMin)
            ->whereNotExists(function ($q) use ($dia, $horaIni, $horaFin) {
                $q->select(DB::raw(1))
                    ->from('horario as h')
                    ->whereColumn('h.id_aula', 'a.id_aula')
                    ->where('h.dia', $dia)
                    ->where('h.estado', 'Activo')
                    ->where(function ($overlap) use ($horaIni, $horaFin) {
                        $overlap->where('h.hora_fin', '>', $horaIni)
                                ->where('h.hora_ini', '<', $horaFin);
                    });
            });

        if ($idModulo !== null) {
            $query->where('a.id_modulo', $idModulo);
        }

        if ($tipoAula !== null && $tipoAula !== '') {
            $query->where('a.tipo_aula', $tipoAula);
        }

        $aulas = $query
            ->select('a.id_aula', 'a.nroaula', 'a.tipo_aula', 'a.capacidad', 'a.disponible', 'a.id_modulo')
            ->orderBy('a.capacidad')
            ->get()
            ->toArray();

        return ['aulas' => $aulas];
    }

    /**
     * Crea un horario con aula asignada para una carga horaria dada.
     * Detecta conflicto si existe un horario Activo del mismo aula/día con solape de tiempo.
     */
    public function crearHorarioConAula(array $data): array
    {
        // Resolver id_aula
        $idAula = $data['id_aula'] ?? null;
        if (!$idAula && isset($data['nroaula'], $data['id_modulo'])) {
            $aula = Aula::where('nroaula', $data['nroaula'])
                ->where('id_modulo', $data['id_modulo'])
                ->first();
            if (!$aula) {
                return ['error' => 'Aula no encontrada'];
            }
            $idAula = $aula->id_aula;
        }

        // Comprobar existencia y disponibilidad del aula
        $aulaRow = DB::table('aula')->where('id_aula', $idAula)->first();
        if (!$aulaRow) {
            return ['error' => 'Aula no encontrada'];
        }
        if (!(bool) $aulaRow->disponible) {
            return ['error' => 'Aula no disponible'];
        }

        // Validar conflicto por solape en horario Activo
        $conflicto = DB::table('horario')
            ->where('id_aula', $idAula)
            ->where('dia', $data['dia'])
            ->where('estado', 'Activo')
            ->where('hora_fin', '>', $data['hora_ini'])
            ->where('hora_ini', '<', $data['hora_fin'])
            ->exists();
        if ($conflicto) {
            return ['error' => 'Conflicto con otra reserva'];
        }

        // Validar existencia de la carga horaria solo si se especifica
        $idCarga = $data['id_carga'] ?? null;
        if ($idCarga !== null) {
            $cargaExiste = DB::table('carga_horaria')->where('id_carga', $idCarga)->exists();
            if (!$cargaExiste) {
                return ['error' => 'Carga horaria no encontrada'];
            }
        }

        $horarioId = DB::table('horario')->insertGetId([
            'id_carga' => $idCarga !== null ? (int) $idCarga : null,
            'id_aula'  => (int) $idAula,
            'dia'      => $data['dia'],
            'hora_ini' => $data['hora_ini'],
            'hora_fin' => $data['hora_fin'],
            'estado'   => $data['estado'] ?? 'Activo',
        ], 'id_horario');

        $horario = DB::table('horario')->where('id_horario', $horarioId)->first();
        return ['ok' => 'Horario creado', 'horario' => $horario];
    }
}
