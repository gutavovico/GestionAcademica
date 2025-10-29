<?php

namespace App\Administracion\Controllers;

use App\Administracion\Services\BitacoraService;
use App\Administracion\Requests\BitacoraFilterRequest;
use App\AutenticacionYSeguridad\Models\Usuario;
use App\Http\Controllers\Controller;

class BitacoraController extends Controller
{
    public function __construct(private BitacoraService $service) {}

    public function index(BitacoraFilterRequest $request)
    {
        $filters = $request->validated();
        $registros = $this->service->search($filters, 20);
        $usuarios = Usuario::orderBy('nombre')->get(['id_usuario','nombre','correo']);
        return view('administracion.bitacora', compact('registros', 'usuarios', 'filters'));
    }
}

