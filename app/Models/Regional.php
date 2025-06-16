<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    use HasFactory;

    /**
     * Atributos asignables.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'departamento_id',
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

        // Convertir el nombre de la regional a mayúsculas antes de guardar.
        static::saving(function ($regional) {
            $regional->nombre = strtoupper($regional->nombre);
        });
    }

    /**
     * Relación: Regional pertenece a un departamento.
     */
    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    /**
     * Relación: Regional tiene muchos municipios.
     */
    public function municipios()
    {
        return $this->hasMany(Municipio::class);
    }

    /**
     * Relación: Regional creada por un usuario.
     */
    public function userCreated()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación: Regional modificada por un usuario.
     */
    public function userEdited()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }
}
