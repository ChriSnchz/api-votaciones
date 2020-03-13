<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Eleccion extends Model
{
    protected $table = 'elecciones';
    protected $primaryKey = 'id_eleccion';
    public $timestamps = false;
}