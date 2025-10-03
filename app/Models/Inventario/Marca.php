<?php

namespace App\Models\Inventario;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Marca extends Model
{
    protected $table = 'marcas';

    protected $fillable = [
        'nombre',
        'user_create_id',
        'user_update_id'
    ];

    // Relación con el usuario que creó
    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    // Relación con el usuario que actualizó
    public function userUpdate()
    {
        return $this->belongsTo(User::class, 'user_update_id');
    }

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}