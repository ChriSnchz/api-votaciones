<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Candidato extends Model
{
    protected $table = 'candidatos';
    protected $primaryKey = 'id_candidato';
    public $timestamps = false;

    public function eleccion()
    {
        return $this->belongsTo('App\Eleccion', 'id_eleccion');
    }
}