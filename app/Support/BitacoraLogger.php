<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BitacoraLogger
{
    public static function log(string $accion, ?string $detalle = null, ?int $idUsuario = null, ?string $ip = null): void
    {
        try {
            $u = $idUsuario ?? (Auth::user()->id_usuario ?? null);
            $addr = $ip ?? request()->ip();
            // Resolver zona horaria similar al registro de asistencia, evitando depender de caché
            $tz  = config('app.timezone', 'America/La_Paz');
            if (!$tz || strtoupper($tz) === 'UTC') {
                $envTz = env('APP_TIMEZONE');
                $tz = $envTz ?: (date_default_timezone_get() ?: 'America/La_Paz');
            }
            $now = Carbon::now($tz);
            DB::table('bitacora')->insert([
                'id_usuario' => $u,
                'accion'     => $accion,
                'detalle'    => $detalle,
                'fecha'      => $now->toDateString(),
                'hora'       => $now->format('H:i:s'),
                'ip'         => $addr,
            ]);
        } catch (\Throwable $e) {
            // No interrumpir el flujo de negocio si falla el log; deja rastro en el log de Laravel
            if (function_exists('logger')) {
                logger()->warning('BitacoraLogger: fallo al insertar (primer intento): '.$e->getMessage());
            }
            // Fallback sin fecha/hora explícitas (deja a la BD los defaults)
            try {
                $u = $idUsuario ?? (Auth::user()->id_usuario ?? null);
                $addr = $ip ?? request()->ip();
                DB::table('bitacora')->insert([
                    'id_usuario' => $u,
                    'accion'     => $accion,
                    'detalle'    => $detalle,
                    'ip'         => $addr,
                ]);
            } catch (\Throwable $e2) {
                if (function_exists('logger')) {
                    logger()->warning('BitacoraLogger: fallo al insertar (fallback): '.$e2->getMessage());
                }
            }
        }
    }
}
