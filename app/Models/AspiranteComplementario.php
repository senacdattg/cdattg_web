<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AspiranteComplementario extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'aspirantes_complementarios';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'persona_id',
        'complementario_id',
        'observaciones',
        'estado',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estado' => 'integer',
    ];

    /**
     * Get the persona associated with the aspirant.
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Get the complementario associated with the aspirant.
     */
    public function complementario()
    {
        return $this->belongsTo(ComplementarioOfertado::class, 'complementario_id');
    }

    /**
     * Get the status label.
     *
     * @return string
     */
    public function getEstadoLabelAttribute()
    {
        return match($this->estado) {
            1 => 'En proceso',
            2 => 'Rechazado',
            3 => 'Admitido',
            default => 'Desconocido'
        };
    }
}
