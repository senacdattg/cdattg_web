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

    /**
     * Calcula las horas de formación para este día.
     *
     * @return string
     */
    public function calcularHorasDia()
    {
        if (!$this->hora_inicio || !$this->hora_fin) {
            return 'Horas no definidas';
        }

        try {
            $inicio = \Carbon\Carbon::parse($this->hora_inicio);
            $fin = \Carbon\Carbon::parse($this->hora_fin);
            $horas = $fin->diffInMinutes($inicio) / 60;
            return number_format($horas, 1) . ' horas/día';
        } catch (\Exception $e) {
            return 'Horas no calculables';
        }
    }
}
