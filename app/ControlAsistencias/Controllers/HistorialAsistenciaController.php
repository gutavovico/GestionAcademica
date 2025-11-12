<?php

namespace App\ControlAsistencias\Controllers;

use App\Http\Controllers\Controller;
use App\ControlAsistencias\Services\HistorialAsistenciaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class HistorialAsistenciaController extends Controller
{
    public function __construct(private HistorialAsistenciaService $service) {}

    // JSON del historial del usuario autenticado (Docente/Coordinador)
    public function miHistorial(Request $request)
    {
        $user = Auth::user();
        if (!$user) return response()->json(['error' => 'No autenticado'], 401);

        $v = Validator::make($request->all(), [
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date',
            'id_materia'  => 'nullable|integer',
            'gestion'     => 'nullable|string|max:20',
            'tipo'        => 'nullable|string|max:20',
            'metodo'      => 'nullable|string|max:20',
            'texto'       => 'nullable|string|max:100',
        ]);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);

        $data = $this->service->buscar($v->validated(), (int) $user->id_usuario);
        return response()->json($data, 200);
    }

    // Búsqueda para Coordinador/Administrador: requiere id_usuario del docente
    public function buscar(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_usuario'  => 'required|integer',
            'fecha_desde' => 'nullable|date',
            'fecha_hasta' => 'nullable|date',
            'id_materia'  => 'nullable|integer',
            'gestion'     => 'nullable|string|max:20',
            'tipo'        => 'nullable|string|max:20',
            'metodo'      => 'nullable|string|max:20',
            'texto'       => 'nullable|string|max:100',
        ]);
        if ($v->fails()) return response()->json(['error' => $v->errors()->first()], 422);
        $d = $v->validated();
        $data = $this->service->buscar($d, (int) $d['id_usuario']);
        return response()->json($data, 200);
    }
}
