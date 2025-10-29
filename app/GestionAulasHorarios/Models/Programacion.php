<?php

namespace App\GestionAulasHorarios\Models;

use Illuminate\Database\Eloquent\Model;

class Programacion extends Model
{
    protected $table = 'programacion';
    protected $primaryKey = 'id_programacion';
    public $timestamps = false;

    protected $fillable = [
        'id_grupo',
        'id_materia',
        'id_aula',
        'dia',
        'bloque',
    ];
}

