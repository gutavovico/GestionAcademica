<?php

namespace App\GestionAulasHorarios\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\GestionAulasHorarios\Services\AulaService;

class AulaController extends Controller
{
    private AulaService $service;

    public function __construct(AulaService $service)
    {
        $this->service = $service;
    }

    public function registrar(Request $request)
    {
        $v = Validator::make($request->all(), [
            'nroaula'   => 'required|integer',
            'id_modulo' => 'required|integer',
            'capacidad' => 'required|integer|min:1',
            'tipo_aula' => 'nullable|string|max:30',
            'disponible'=> 'nullable|boolean',
        ]);

        if ($v->fails()) {
            return response()->json(['error' => $v->errors()->first()], 422);
        }

        $res = $this->service->registrarAula($v->validated(), null);

        if (isset($res['error'])) {
            $code = match ($res['error']) {
                'Capacidad inválida' => 422,
                'Módulo inexistente' => 404,
                default => 409,
            };
            return response()->json($res, $code);
        }

        return response()->json($res, 201);
    }
}
