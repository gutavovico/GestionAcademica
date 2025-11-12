<?php

namespace App\Administracion\Models;

use Illuminate\Database\Eloquent\Model;
use App\AutenticacionYSeguridad\Models\Usuario;

class Bitacora extends Model
{
    protected $table = 'bitacora';
    protected $primaryKey = 'id_bitacora';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'accion',
        'detalle',
        'fecha',
        'hora',
        'ip',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Representación amigable de la hora (recorta microsegundos y mantiene HH:mm:ss)
    public function getHoraTextoAttribute(): string
    {
        $h = (string) ($this->attributes['hora'] ?? '');
        if ($h === '') return '';
        // Tomar solo HH:mm:ss al inicio, ignorando fracción si existe
        if (preg_match('/^(\d{2}:\d{2}:\d{2})/', $h, $m)) {
            return $m[1];
        }
        return substr($h, 0, 8);
    }
}
