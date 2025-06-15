<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NivelFormacion extends Model
{
    protected $table = 'niveles_formacion';
    protected $fillable = ['nivel_formacion'];

    public function programasFormacion()
    {
        return $this->hasMany(ProgramaFormacion::class);
    }
}
