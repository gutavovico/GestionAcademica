<?php

namespace App\GestionAulasHorarios\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horario';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;

    protected $fillable = [
        'id_carga',
        'id_aula',
        'dia',
        'hora_ini',
        'hora_fin',
        'estado',
    ];
}

