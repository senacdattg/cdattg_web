<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplementarioOfertado extends Model
{
    protected $table = 'complementarios_ofertados';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'duracion',
        'cupos',
        'estado',
        'modalidad_id',
        'jornada_id',
        'ambiente_id',
    ];

    public function modalidad()
    {
        return $this->belongsTo(ParametroTema::class, 'modalidad_id');
    }

    public function jornada()
    {
        return $this->belongsTo(JornadaFormacion::class, 'jornada_id');
    }

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class, 'ambiente_id');
    }

    public function diasFormacion()
    {
        return $this->belongsToMany(Parametro::class, 'complementarios_ofertados_dias_formacion', 'complementario_id', 'dia_id')
                    ->withPivot('hora_inicio', 'hora_fin');
    }

    public function getEstadoLabelAttribute()
    {
        return match ($this->estado) {
            0 => 'Sin Oferta',
            1 => 'Con Oferta',
            2 => 'Cupos Llenos',
            default => 'Desconocido',
        };
    }

    public function getBadgeClassAttribute()
    {
        return match ($this->estado) {
            0 => 'bg-success',
            1 => 'bg-warning',
            2 => 'bg-danger',
            default => 'bg-secondary',
        };
    }

    public function getIconoAttribute()
    {
        $iconos = [
            'Auxiliar de Cocina' => 'fas fa-utensils',
            'Acabados en Madera' => 'fas fa-hammer',
            'Confección de Prendas' => 'fas fa-cut',
            'Mecánica Básica Automotriz' => 'fas fa-car',
            'Cultivos de Huertas Urbanas' => 'fas fa-spa',
            'Normatividad Laboral' => 'fas fa-gavel',
        ];

        return $iconos[$this->nombre] ?? 'fas fa-graduation-cap';
    }
}
