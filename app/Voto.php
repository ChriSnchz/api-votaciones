<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Voto extends Model
{
    protected $tabla = 'votos';
    public $timestamps = false;
    protected $primaryKey = 'id';

    public function candidato()
    {
        return $this->belongsTo('App\Candidato', 'id_candidato');
    }
}
