<?php

namespace App\GestionAcademica\Controllers;

use App\GestionAcademica\Models\CargaHoraria;
use App\GestionAcademica\Models\Materia;
use App\GestionAcademica\Models\Grupo;
use App\AutenticacionYSeguridad\Models\Usuario;
use App\GestionAcademica\Requests\CargaHorariaStoreRequest;
use App\GestionAcademica\Services\CargaHorariaService;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

class CargaHorariaController extends Controller
{
    public function __construct(private CargaHorariaService $service) {}

    public function index()
    {
        $docentes = Usuario::with('rol')->whereHas('rol', fn($q) => $q->where('nombre', 'Docente'))->orderBy('nombre')->get();
        $materias = Materia::orderBy('nombre')->get();
        $grupos = Grupo::orderBy('nombre')->get();
        $cargas = CargaHoraria::with(['docente','materia','grupo'])->orderBy('id_carga', 'desc')->paginate(20);
        return view('admin.cargas', compact('docentes','materias','grupos','cargas'));
    }

    public function store(CargaHorariaStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->assign($data);
        return back()->with('success', 'Carga horaria asignada');
    }

    /* Eliminación deshabilitada por requerimiento
    public function destroy(int $id): RedirectResponse { ... }
    */
}
