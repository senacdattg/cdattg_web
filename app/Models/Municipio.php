<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    use HasFactory;
    protected $fillable = [
        'municipio',
        'departamento_id',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($municipio) {
            $municipio->municipio = strtoupper($municipio->municipio);
        });
    }

    /**
     * Relación: Un municipio pertenece a una regional.
     */
    public function regional()
    {
        return $this->belongsTo(Regional::class);
    }

    /**
     * Relación: Un municipio tiene muchos centros de formación.
     */
    public function centrosFormacion()
    {
        return $this->hasMany(CentroFormacion::class);
    }
}
