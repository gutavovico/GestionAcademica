<?php

namespace App\AutenticacionYSeguridad\Controllers;

use App\AutenticacionYSeguridad\Requests\LoginRequest;
use App\AutenticacionYSeguridad\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Muestra el formulario de inicio de sesión.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Maneja el intento de inicio de sesión.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('correo', 'contrasena');
        $correo = $credentials['correo'];
        $contrasena = $credentials['contrasena'];

        if ($this->authService->attemptLogin($correo, $contrasena)) {
            $request->session()->regenerate();

            return redirect()->intended('/dashboard')
                             ->with('success', 'Bienvenido de nuevo!');
        }

        return back()->withErrors([
            'correo' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    /**
     * Cierra la sesión del usuario.
     */
    public function logout(Request $request): RedirectResponse
    {
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
                ->with('info', 'Sesión cerrada correctamente.');
    }
}

