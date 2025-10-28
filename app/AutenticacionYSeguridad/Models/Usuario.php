<?php

namespace App\AutenticacionYSeguridad\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'correo',
        'telefono',
        'contrasena',
        'id_rol',
    ];

    public function getAuthPassword()
    {
        return $this->contrasena;
    }

    protected $hidden = [
        'contrasena',
    ];
}

