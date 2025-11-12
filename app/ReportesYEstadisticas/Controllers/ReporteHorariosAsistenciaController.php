<?php

namespace App\ReportesYEstadisticas\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\ReportesYEstadisticas\Services\ReporteHorariosAsistenciaService;

class ReporteHorariosAsistenciaController extends Controller
{
    public function __construct(private ReporteHorariosAsistenciaService $service) {}

    public function vista()
    {
        return view('reportes.horarios_asistencia');
    }

    public function opciones()
    {
        return response()->json($this->service->opciones(), 200);
    }

    public function data(Request $request)
    {
        $v = Validator::make($request->all(), [
            'desde'      => 'required|date',
            'hasta'      => 'required|date',
            'id_usuario' => 'nullable|integer',
            'id_materia' => 'nullable|integer',
            'id_grupo'   => 'nullable|integer',
            'gestion'    => 'nullable|string|max:20',
        ]);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);
        $d = $v->validated();
        $res = $this->service->data($d['desde'], $d['hasta'], $d['id_usuario'] ?? null, $d['id_materia'] ?? null, $d['id_grupo'] ?? null, $d['gestion'] ?? null);
        return response()->json($res, 200);
    }
}

