<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    protected $tabla = 'candidatos';
    public $timestamps = false;

    public function eleccion(){
        return $this->belongsTo('App\Eleccion', 'id_eleccion');
    }
}
