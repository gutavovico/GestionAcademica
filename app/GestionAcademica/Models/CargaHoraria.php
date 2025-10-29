<?php

namespace App\GestionAcademica\Models;

use Illuminate\Database\Eloquent\Model;
use App\AutenticacionYSeguridad\Models\Usuario;

class CargaHoraria extends Model
{
    protected $table = 'carga_horaria';
    protected $primaryKey = 'id_carga';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario',
        'id_materia',
        'id_grupo',
        'horas_semanales',
        'gestion',
        'fecha_asignacion',
        'estado',
    ];

    public function docente()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }
}

