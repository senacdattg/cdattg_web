<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModalidadFormacion extends Model
{
    protected $table = 'modalidades_formacion';
    protected $fillable = ['modalidad_formacion'];

    public function fichasCaracterizacion()
    {
        return $this->hasMany(FichaCaracterizacion::class);
    }
}
