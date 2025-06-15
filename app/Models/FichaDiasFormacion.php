<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FichaDiasFormacion extends Model
{
    protected $table = "ficha_dias_formacion";

    protected $fillable = [
        "ficha_id",
        "dia_id",
        "hora_inicio",
        "hora_fin"
    ];

    public function ficha()
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_id');
    }

    public function dia()
    {
        return $this->belongsTo(DiaFormacion::class, 'dia_id');
    }
}
