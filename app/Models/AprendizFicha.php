<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AprendizFicha extends Model
{
    protected $table = 'aprendiz_fichas_caracterizacion';
    protected $fillable = ['aprendiz_id', 'ficha_id'];
}
