<?php

namespace App\Models\Inventario;

use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use Seguimiento;

    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
        'user_create_id',
        'user_update_id'
    ];

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}