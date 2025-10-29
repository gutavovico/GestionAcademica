<?php

namespace App\AutenticacionYSeguridad\Controllers;

use App\AutenticacionYSeguridad\Models\Usuario;
use App\AutenticacionYSeguridad\Models\Rol;
use App\AutenticacionYSeguridad\Requests\UsuarioStoreRequest;
use App\AutenticacionYSeguridad\Requests\UsuarioUpdateRequest;
use App\AutenticacionYSeguridad\Services\UsuarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class UsuarioAdminController extends Controller
{
    public function __construct(private UsuarioService $service)
    {
    }

    public function index()
    {
        $usuarios = Usuario::with('rol')->orderBy('id_usuario', 'desc')->paginate(15);
        $roles = Rol::orderBy('nombre')->get();
        return view('admin.users', compact('usuarios', 'roles'));
    }

    public function store(UsuarioStoreRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return back()->with('success', 'Usuario creado correctamente');
    }

    public function update(UsuarioUpdateRequest $request, int $id): RedirectResponse
    {
        $usuario = Usuario::findOrFail($id);
        $this->service->update($usuario, $request->validated());
        return back()->with('success', 'Usuario actualizado correctamente');
    }

    public function toggleEstado(int $id): RedirectResponse
    {
        $usuario = Usuario::findOrFail($id);
        $this->service->toggleEstado($usuario);
        return back()->with('success', 'Estado del usuario actualizado');
    }
}

