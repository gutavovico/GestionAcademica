<?php

namespace App\GestionAcademica\Controllers;

use App\GestionAcademica\Models\Grupo;
use App\GestionAcademica\Requests\GrupoStoreRequest;
use App\GestionAcademica\Requests\GrupoUpdateRequest;
use App\GestionAcademica\Services\GrupoService;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Support\BitacoraLogger;

class GrupoController extends Controller
{
    public function __construct(private GrupoService $service) {}

    public function index()
    {
        $grupos = Grupo::orderBy('id_grupo','desc')->paginate(20);
        return view('admin.grupos', compact('grupos'));
    }

    public function store(GrupoStoreRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->service->create($data);
        BitacoraLogger::log('Crear grupo', ($data['nombre'] ?? ''));
        return back()->with('success', 'Grupo creado');
    }

    /* Edición y eliminación deshabilitadas por requerimiento
    public function update(GrupoUpdateRequest $request, int $id): RedirectResponse { ... }
    public function destroy(int $id): RedirectResponse { ... }
    */
}
