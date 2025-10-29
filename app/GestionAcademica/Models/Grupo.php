<?php

namespace App\GestionAcademica\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupo';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'turno',
        'capacidad_max',
    ];
}

