<?php

namespace App\Models\Inventario;

use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use Seguimiento;

    protected $table = 'proveedores';

    protected $fillable = [
        'proveedor',
        'nit',
        'email',
        'user_create_id',
        'user_update_id'
    ];

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