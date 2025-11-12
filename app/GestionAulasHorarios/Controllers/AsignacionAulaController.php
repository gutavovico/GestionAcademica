<?php

namespace App\GestionAulasHorarios\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\GestionAulasHorarios\Services\AsignacionAulaService;
use App\Support\BitacoraLogger;
use App\Support\BitacoraLogger;

class AsignacionAulaController extends Controller
{
    private AsignacionAulaService $service;

    public function __construct(AsignacionAulaService $service)
    {
        $this->service = $service;
    }

    /**
     * GET /api/asignacion/aulas?capacidad=&id_modulo=&tipo_aula=
     */
    public function aulas(Request $request)
    {
        $v = Validator::make($request->all(), [
            'capacidad' => 'nullable|integer|min:1',
            'id_modulo' => 'nullable|integer',
            'tipo_aula' => 'nullable|string|max:30',
        ]);
        if ($v->fails()) {
            return response()->json(['error' => $v->errors()->first()], 422);
        }
        $d = $v->validated();
        $res = $this->service->listarAulas($d['capacidad'] ?? null, $d['id_modulo'] ?? null, $d['tipo_aula'] ?? null);
        BitacoraLogger::log('Listar aulas', json_encode($d));
        return response()->json($res, 200);
    }

    /**
     * GET /api/asignacion/modulos
     */
    public function modulos()
    {
        return response()->json($this->service->listarModulos(), 200);
    }

    /**
     * GET /api/asignacion/slots?dia=&id_aula=
     */
    public function slots(Request $request)
    {
        $v = Validator::make($request->all(), [
            'dia'     => 'required|string|max:15',
            'id_aula' => 'required|integer',
        ]);
        if ($v->fails()) {
            return response()->json(['error' => $v->errors()->first()], 422);
        }
        $d = $v->validated();
        $res = $this->service->slotsDisponibles((int)$d['id_aula'], $d['dia']);
        return response()->json($res, 200);
    }

    /**
     * GET /api/asignacion/aulas-disponibles?dia=&hora_ini=&hora_fin=&capacidad=&id_modulo=&tipo_aula=
     */
    public function disponibles(Request $request)
    {
        $v = Validator::make($request->all(), [
            'dia'        => 'required|string|max:15',
            'hora_ini'   => 'required|date_format:H:i',
            'hora_fin'   => 'required|date_format:H:i',
            'capacidad'  => 'required|integer|min:1',
            'id_modulo'  => 'nullable|integer',
            'tipo_aula'  => 'nullable|string|max:30',
        ]);

        if ($v->fails()) {
            return response()->json(['error' => $v->errors()->first()], 422);
        }

        $data = $v->validated();

        if (strcmp($data['hora_ini'], $data['hora_fin']) >= 0) {
            return response()->json(['error' => 'hora_ini debe ser menor que hora_fin'], 422);
        }

        $res = $this->service->listarAulasDisponibles(
            $data['dia'],
            $data['hora_ini'],
            $data['hora_fin'],
            (int) $data['capacidad'],
            $data['id_modulo'] ?? null,
            $data['tipo_aula'] ?? null,
        );
        BitacoraLogger::log('Consultar aulas disponibles', json_encode($data));
        return response()->json($res, 200);
    }

    /**
     * POST /api/asignacion/asignar
     * Crea un nuevo horario con un aula asignada para una carga horaria.
     * Requiere id_carga, dia, hora_ini, hora_fin y id_aula o (nroaula + id_modulo).
     */
    public function asignar(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_carga'   => 'nullable|integer',
            'dia'        => 'required|string|max:15',
            'hora_ini'   => 'required|date_format:H:i',
            'hora_fin'   => 'required|date_format:H:i',

            'id_aula'    => 'required_without:nroaula|integer',
            'nroaula'    => 'required_without:id_aula|integer',
            'id_modulo'  => 'required_with:nroaula|integer',
        ]);

        if ($v->fails()) {
            return response()->json(['error' => $v->errors()->first()], 422);
        }

        $data = $v->validated();
        if (strcmp($data['hora_ini'], $data['hora_fin']) >= 0) {
            return response()->json(['error' => 'hora_ini debe ser menor que hora_fin'], 422);
        }

        $res = $this->service->crearHorarioConAula($data);

        if (isset($res['error'])) {
            $code = match ($res['error']) {
                'Conflicto con otra reserva' => 409,
                'Aula no disponible' => 409,
                'Aula no encontrada' => 404,
                'Carga horaria no encontrada' => 404,
                default => 400,
            };
            return response()->json($res, $code);
        }

        if (!isset($res['error'])) {
            BitacoraLogger::log('Asignar aula', 'Día '.$data['dia'].' '.$data['hora_ini'].'-'.$data['hora_fin'].' (aula '.($data['id_aula'] ?? ($data['nroaula'].'/M'.$data['id_modulo'])).')');
        }
        return response()->json($res, 201);
    }
}
