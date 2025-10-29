<?php

namespace App\GestionAulasHorarios\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\GestionAulasHorarios\Services\HorarioService;

class HorarioController extends Controller
{
    public function __construct(private HorarioService $service) {}

    // GET /api/horarios
    public function index(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_usuario' => 'nullable|integer',
            'dia'        => 'nullable|string|max:15',
            'estado'     => 'nullable|string|max:20',
        ]);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);
        return response()->json($this->service->listar($v->validated()), 200);
    }

    // GET /api/horarios/{id}
    public function show(int $id)
    {
        $r = $this->service->obtener($id);
        return response()->json($r, isset($r['error']) ? 404 : 200);
    }

    // POST /api/horarios
    public function store(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_carga' => 'nullable|integer',
            'id_aula'  => 'required|integer',
            'dia'      => 'required|string|max:15',
            'hora_ini' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i',
            'estado'   => 'nullable|string|max:20',
        ]);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);
        $d = $v->validated();
        if (strcmp($d['hora_ini'], $d['hora_fin']) >= 0) {
            return response()->json(['error' => 'hora_ini debe ser menor que hora_fin'], 422);
        }
        $r = $this->service->crear($d);
        return response()->json($r, isset($r['error']) ? ($r['error']==='Conflicto con otra reserva'?409:404) : 201);
    }

    // PUT /api/horarios/{id}
    public function update(Request $request, int $id)
    {
        $v = Validator::make($request->all(), [
            'id_carga' => 'nullable',
            'id_aula'  => 'nullable|integer',
            'dia'      => 'nullable|string|max:15',
            'hora_ini' => 'nullable|date_format:H:i',
            'hora_fin' => 'nullable|date_format:H:i',
            'estado'   => 'nullable|string|max:20',
        ]);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);
        $d = $v->validated();
        if (isset($d['hora_ini'], $d['hora_fin']) && strcmp($d['hora_ini'], $d['hora_fin']) >= 0) {
            return response()->json(['error' => 'hora_ini debe ser menor que hora_fin'], 422);
        }
        $r = $this->service->actualizar($id, $d);
        return response()->json($r, isset($r['error']) ? ($r['error']==='Conflicto con otra reserva'?409:404) : 200);
    }

    // DELETE /api/horarios/{id}  -> marca estado='Activo'
    public function destroy(int $id)
    {
        $r = $this->service->marcarAsignado($id);
        return response()->json($r, isset($r['error']) ? 404 : 200);
    }

    // GET /api/horarios/docentes
    public function docentes()
    {
        return response()->json($this->service->listarDocentes(), 200);
    }

    // GET /api/horarios/cargas?id_usuario=
    public function cargas(Request $request)
    {
        $v = Validator::make($request->all(), [ 'id_usuario' => 'required|integer' ]);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);
        $d = $v->validated();
        return response()->json($this->service->cargasPorDocente((int)$d['id_usuario']), 200);
    }
}
