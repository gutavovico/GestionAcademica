<?php

namespace App\GestionAcademica\Controllers;

use App\GestionAcademica\Models\Materia;
use App\GestionAcademica\Requests\MateriaStoreRequest;
use App\GestionAcademica\Requests\MateriaUpdateRequest;
use App\GestionAcademica\Services\MateriaService;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

class MateriaController extends Controller
{
    public function __construct(private MateriaService $service) {}

    public function index()
    {
        $materias = Materia::orderBy('id_materia','desc')->paginate(20);
        return view('admin.materias', compact('materias'));
    }

    public function store(MateriaStoreRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return back()->with('success', 'Materia creada');
    }

    /* Edición y eliminación deshabilitadas por requerimiento
    public function update(MateriaUpdateRequest $request, int $id): RedirectResponse { ... }
    public function destroy(int $id): RedirectResponse { ... }
    */
}
