<?php

namespace App\Models\Inventario;

use App\Models\Parametro;
use App\Models\ParametroTema;
use App\Models\Tema;

class Categoria extends Parametro
{
    protected static function booted()
    {
        static::creating(function ($categoria) {
            $categoria->name = strtoupper($categoria->name);
        });
    }

    // Relación con el tema "CATEGORÍAS".
    public static function tema()
    {
        return Tema::where('name', 'categoriaS')->first();
    }

    // Guardar la categoria como parámetro asociado al tema "categoriaS".
    public function asociarATemacategorias()
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

    public function productos()
    {
        return $this->hasMany(Producto::class, 'id_categoria'); 
    }
}
