<?php

namespace App\AutenticacionYSeguridad\Controllers;

use App\AutenticacionYSeguridad\Models\Rol;
use App\AutenticacionYSeguridad\Requests\RolStoreRequest;
use App\AutenticacionYSeguridad\Requests\RolUpdateRequest;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;

class RolAdminController extends Controller
{
    public function index()
    {
        $roles = Rol::orderBy('nombre')->get();
        return view('admin.roles', compact('roles'));
    }

    public function store(RolStoreRequest $request): RedirectResponse
    {
        Rol::create($request->validated());
        return back()->with('success', 'Rol creado correctamente');
    }

    public function update(RolUpdateRequest $request, int $id): RedirectResponse
    {
        $rol = Rol::findOrFail($id);
        $rol->update($request->validated());
        return back()->with('success', 'Rol actualizado correctamente');
    }

    public function destroy(int $id): RedirectResponse
    {
        $rol = Rol::findOrFail($id);
        if ($rol->usuarios()->exists()) {
            return back()->withErrors(['rol' => 'No se puede eliminar un rol asignado a usuarios.']);
        }
        $rol->delete();
        return back()->with('success', 'Rol eliminado');
    }
}

