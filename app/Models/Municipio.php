<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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

        static::saving(function (self $municipio): void {
            $municipio->municipio = $municipio->municipio !== null
                ? Str::upper($municipio->municipio)
                : null;
        });
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
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
