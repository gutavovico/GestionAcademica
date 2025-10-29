<?php

namespace App\GestionAulasHorarios\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'aula';
    protected $primaryKey = 'id_aula';
    public $timestamps = false;

    protected $fillable = ['nroaula','tipo_aula','capacidad','disponible','id_modulo'];
}
