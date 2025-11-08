<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CategoriaCaracterizacionComplementario extends Model
{
    use HasFactory;

    protected $table = 'categorias_caracterizacion_complementarios';

    protected $fillable = [
        'nombre',
        'slug',
        'activo',
        'parent_id'
    ];

    /**
     * Obtener la categoría padre
     */
    public function parent()
    {
        return $this->belongsTo(CategoriaCaracterizacionComplementario::class, 'parent_id');
    }

    /**
     * Obtener las categorías hijas
     */
    public function children()
    {
        return $this->hasMany(CategoriaCaracterizacionComplementario::class, 'parent_id');
    }

    /**
     * Obtener las categorías principales (sin padre)
     */
    public static function getMainCategories()
    {
        return self::whereNull('parent_id')->where('activo', 1)->get();
    }

    /**
     * Obtener las categorías hijas activas
     */
    public function getActiveChildren()
    {
        return $this->children()->where('activo', 1)->get();
    }

    public function personas(): BelongsToMany
    {
        return $this->belongsToMany(
            Persona::class,
            'persona_caracterizacion',
            'parametro_id',
            'persona_id'
        )->withTimestamps();
    }
}
