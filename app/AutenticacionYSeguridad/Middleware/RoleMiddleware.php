<?php

namespace App\AutenticacionYSeguridad\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        $allowed = collect($roles)->filter()->map('strtolower');
        $userRole = strtolower((string) optional($user->rol)->nombre);

        if ($allowed->isEmpty() || $allowed->contains($userRole)) {
            return $next($request);
        }

        abort(403, 'No tienes permisos para esta acción');
    }
}

