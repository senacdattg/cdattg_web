<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiasFormacion extends Model
{
    protected $table = "dias_formacion";

    protected $fillable = [
        "nombre"
    ];
}
