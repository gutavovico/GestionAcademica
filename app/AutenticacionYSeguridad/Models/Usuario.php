<?php

namespace App\AutenticacionYSeguridad\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;
use App\AutenticacionYSeguridad\Notifications\ResetPasswordNotification;
use App\AutenticacionYSeguridad\Models\Rol;

class Usuario extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

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

    public function getEmailForPasswordReset()
    {
        return $this->correo;
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol', 'id_rol');
    }
}
