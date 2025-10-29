<?php

namespace App\GestionAulasHorarios\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureScheduling
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Implement your scheduling logic here

        return $next($request);
    }
}