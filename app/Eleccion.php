<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eleccion extends Model
{
    protected $tabla = 'elecciones';
    public $timestamps = false;


    public function candidatos(){
        return $this->hasMany('Candidato', 'id_eleccion');
    }
}
