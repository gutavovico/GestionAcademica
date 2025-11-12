<?php

namespace App\ControlAsistencias\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\ControlAsistencias\Services\RegistroAsistenciaService;

class RegistroAsistenciaController extends Controller
{
    private RegistroAsistenciaService $service;

    public function __construct(RegistroAsistenciaService $service)
    {
        $this->service = $service;
    }

    public function vistaDocente()
    {
        $user = Auth::user();
        return view('control_asistencias.registrar_asistencia', compact('user'));
    }

    public function horariosHoy()
    {
        $user = Auth::user();
        $data = $this->service->misHorariosHoy((int)$user->id_usuario);
        return response()->json($data, 200);
    }

    public function registrarDocente(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_horario' => 'required|integer',
            'metodo' => 'nullable|string|max:20',
            'tipo' => 'nullable|string|in:Presente,Ausente,Retraso',
            'observacion' => 'nullable|string|max:1000',
        ]);
        if ($v->fails()) return response()->json(['error'=>$v->errors()->first()], 422);
        $d = $v->validated();
        $user = Auth::user();
        $res = $this->service->registrarParaDocente((int)$user->id_usuario, (int)$d['id_horario'], $d['metodo'] ?? 'Manual', $d['tipo'] ?? null, $d['observacion'] ?? null);
        $code = isset($res['error']) ? 400 : 201;
        return response()->json($res, $code);
    }

    public function registrarAdmin(Request $request)
    {
        $v = Validator::make($request->all(), [
            'id_horario' => 'required|integer',
            'tipo' => 'required|string|in:Presente,Ausente,Retraso',
            'metodo' => 'nullable|string|max:20',
            'observacion' => 'nullable|string|max:1000',
        ]);
        if ($v->fails()) return response()->json(['error'=>$v->errors()->first()], 422);
        $d = $v->validated();
        $res = $this->service->registrarComoAdmin((int)$d['id_horario'], $d['tipo'], $d['metodo'] ?? 'Manual', $d['observacion'] ?? null);
        $code = isset($res['error']) ? 400 : 201;
        return response()->json($res, $code);
    }

    public function vistaAdmin()
    {
        return view('administracion.control_asistencia');
    }

    public function horariosHoyAdmin(Request $request)
    {
        $dia = $request->query('dia');
        $nombre = $request->query('nombre');
        $idUsuario = $request->query('id_usuario');
        $data = $this->service->horariosHoyAdmin($dia ?: null, $nombre ?: null, $idUsuario ? (int)$idUsuario : null);
        return response()->json($data, 200);
    }
}
