<?php

namespace App\Models\Inventario;

use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Marca extends Parametro
{
    protected $table = 'parametros';

    protected static function booted() : void
    {
        static::creating(function ($marca) : void {
            $marca->name = strtoupper($marca->name);
        });
    }

    // Relación con el tema "MARCAS".
    public static function tema() : ?Tema
    {
        return Tema::where('name', 'MARCAS')->first();
    }

    // Guardar la marca como parámetro asociado al tema "MARCAS".
    public function asociarATemaMarcas() : void
    {
        $tema = self::tema();

        if ($tema) {
            ParametroTema::create([
                'parametro_id'  => $this->id,
                'tema_id'       => $tema->id,
                'status'        => 1,
                'user_create_id'=> $this->user_create_id,
                'user_edit_id'  => $this->user_edit_id,
            ]);
        }
    }

    public function productos() : HasMany
    {
        return $this->hasMany(Producto::class, 'marca_id');
    }
}
