<?php

namespace App\AutenticacionYSeguridad\Controllers;

use App\AutenticacionYSeguridad\Requests\LoginRequest;
use App\AutenticacionYSeguridad\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Support\BitacoraLogger;


class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Muestra el formulario de inicio de sesi?n.
     */
    public function showLoginForm()
    {
        return view('login');
    }

    /**
     * Maneja el intento de inicio de sesi?n.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->only('correo', 'contrasena');
        $correo = $credentials['correo'];
        $contrasena = $credentials['contrasena'];

        if ($this->authService->attemptLogin($correo, $contrasena)) {
            $request->session()->regenerate();
            BitacoraLogger::log('Login', $correo);
            return redirect()->intended('/dashboard')
                             ->with('success', 'Bienvenido de nuevo!');
        }

        return back()->withErrors([
            'correo' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('correo');
    }

    /**
     * Cierra la sesi?n del usuario.
     */
    public function logout(Request $request): RedirectResponse
    {
        BitacoraLogger::log('Logout', optional($request->user())->correo);
        $this->authService->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
                ->with('info', 'Sesi?n cerrada correctamente.');
    }
}
