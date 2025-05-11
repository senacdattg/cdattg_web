<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsignacionDeFormacion extends Model
{
    /** @use HasFactory<\Database\Factories\AsignacionDeFormacionFactory> */
    use HasFactory;
    protected $fillable = ['id_instructor','id_ficha', 'id_ambiente', 'id_jornada', 'fecha_inicio', 'fecha_fin'];

}
