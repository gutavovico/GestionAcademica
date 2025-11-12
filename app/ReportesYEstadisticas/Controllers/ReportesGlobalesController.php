<?php

namespace App\ReportesYEstadisticas\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\ReportesYEstadisticas\Services\ReportesEstadisticosService;

class ReportesGlobalesController extends Controller
{
    private ReportesEstadisticosService $service;

    public function __construct(ReportesEstadisticosService $service)
    {
        $this->service = $service;
    }

    public function vista()
    {
        return view('administracion.reportes_globales');
    }

    public function opciones()
    {
        return response()->json($this->service->opciones(), 200);
    }

    public function data(Request $request)
    {
        $v = Validator::make($request->all(), [
            'gestion' => 'nullable|string|max:20',
            'id_modulo' => 'nullable|integer',
        ]);
        if ($v->fails()) {
            return response()->json(['error' => $v->errors()->first()], 422);
        }
        $d = $v->validated();
        $res = $this->service->data($d['gestion'] ?? null, isset($d['id_modulo']) ? (int)$d['id_modulo'] : null);
        return response()->json($res, 200);
    }
}

