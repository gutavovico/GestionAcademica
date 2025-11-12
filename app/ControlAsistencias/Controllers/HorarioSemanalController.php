<?php

namespace App\ControlAsistencias\Controllers;

use App\Http\Controllers\Controller;
use App\ControlAsistencias\Services\VerHorarioSemanalService;
use Illuminate\Support\Facades\Auth;

class HorarioSemanalController extends Controller
{
    public function __construct(private VerHorarioSemanalService $service) {}

    // Vista protegida para Docente
    public function vista()
    {
        $user = Auth::user();
        return view('control_asistencias.horario_semanal', [
            'user' => $user,
            'idUsuario' => optional($user)->id_usuario,
        ]);
    }

    // Datos JSON del horario semanal del usuario autenticado
    public function miHorario()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'No autenticado'], 401);
        }
        $data = $this->service->paraDocente((int) $user->id_usuario);
        return response()->json($data, 200);
    }
}

