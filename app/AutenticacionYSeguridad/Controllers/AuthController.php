<?php

namespace App\AutenticacionYSeguridad\Controllers;

use App\AutenticacionYSeguridad\Requests\LoginRequest;
use App\AutenticacionYSeguridad\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    protected $authService;

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
        // Obtener el correo y la contraseña validados
        $credentials = $request->only('correo', 'contrasena');
        $correo = $credentials['correo'];
        $contrasena = $credentials['contrasena'];

        // Intentar autenticar a través del servicio
        if ($this->authService->attemptLogin($correo, $contrasena)) {
            // Regenerar la sesión para prevenir ataques de fijación de sesión
            $request->session()->regenerate();

            // Redirigir a una ruta segura (ej. dashboard)
            return redirect()->intended('/dashboard')
                             ->with('success', '¡Bienvenido de nuevo!');
        }

        // Si la autenticación falla, redirigir al formulario de login con error
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

        // Invalidar la sesión y regenerar el token CSRF
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
                ->with('info', 'Sesión cerrada correctamente.');
    }

}
