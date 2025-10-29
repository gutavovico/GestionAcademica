<?php

namespace App\GestionAcademica\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    public $timestamps = false;

    protected $fillable = [
        'sigla',
        'nombre',
        'semestre',
        'creditos',
        'horas_semana',
    ];
}

