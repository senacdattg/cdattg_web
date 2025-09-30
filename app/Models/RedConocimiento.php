<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedConocimiento extends Model
{
    use HasFactory;

    /**
     * Atributos asignables.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'regionals_id',
        'user_create_id',
        'user_edit_id',
        'status',
    ];

    /**
     * Boot del modelo para eventos.
     */
    protected static function boot()
    {
        parent::boot();

        // Convertir el nombre a mayúsculas antes de guardar
        static::saving(function ($redConocimiento) {
            $redConocimiento->nombre = strtoupper($redConocimiento->nombre);
        });
    }

    /**
     * Relación: Red de conocimiento pertenece a una regional.
     */
    public function regional()
    {
        return $this->belongsTo(Regional::class, 'regionals_id');
    }

    /**
     * Relación: Red de conocimiento creada por un usuario.
     */
    public function userCreated()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación: Red de conocimiento modificada por un usuario.
     */
    public function userEdited()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    /**
     * Relación: Red de conocimiento tiene muchos programas de formación.
     */
    public function programasFormacion()
    {
        return $this->hasMany(ProgramaFormacion::class, 'red_conocimiento_id');
    }
}
