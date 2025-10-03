<?php

namespace App\Models\Inventario;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    protected $table = 'proveedores';

    protected $fillable = [
        'proveedor',
        'nit',
        'email',
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

    // Relación con contratos y convenios
    public function contratosConvenios()
    {
        return $this->hasMany(ContratoConvenio::class);
    }

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}