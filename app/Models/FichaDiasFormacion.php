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
        return $this->belongsTo(Parametro::class, 'dia_id');
    }

    /**
     * Trae todos los días de formación asociados a la ficha.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getDiasFormacionFicha()
    {
        return self::where('ficha_id', $this->ficha_id)->get();
    }
}
