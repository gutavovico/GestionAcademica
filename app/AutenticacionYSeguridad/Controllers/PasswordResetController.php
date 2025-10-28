<?php

namespace App\AutenticacionYSeguridad\Controllers;

use App\AutenticacionYSeguridad\Models\Usuario;
use App\AutenticacionYSeguridad\Requests\ForgotPasswordRequest;
use App\AutenticacionYSeguridad\Requests\ResetPasswordRequest;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('password_forgot');
    }

    public function sendResetLinkEmail(ForgotPasswordRequest $request): RedirectResponse
    {
        $correo = $request->input('correo');
        // Pasamos SOLO 'correo' para que el provider consulte la columna correcta
        $status = Password::sendResetLink([
            'correo' => $correo,
        ]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['correo' => __($status)]);
    }

    public function showResetForm(Request $request, string $token)
    {
        // El broker incluye ?email= en el enlace, úsalo para prellenar
        return view('password_reset', [
            'token' => $token,
            'email' => (string) $request->query('email'),
        ]);
    }

    public function reset(ResetPasswordRequest $request): RedirectResponse
    {
        $correo = $request->input('correo');
        // Usamos SOLO 'correo'; el broker recupera el usuario por 'correo'
        $status = Password::reset(
            [
                'correo' => $correo,
                'password' => $request->input('password'),
                'password_confirmation' => $request->input('password_confirmation'),
                'token' => $request->input('token'),
            ],
            function (Usuario $user, string $password) {
                $user->forceFill([
                    'contrasena' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['correo' => __($status)]);
    }
}
