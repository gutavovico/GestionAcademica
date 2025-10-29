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
}

