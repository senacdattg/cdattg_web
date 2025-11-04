<?php

namespace App\Models\Inventario;

use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use Seguimiento;

    protected $table = 'proveedores';

    protected static function booted()
    {
        static::creating(function ($proveedor) {
            $proveedor->proveedor = strtoupper($proveedor->proveedor);
        });

        static::updating(function ($proveedor) {
            $proveedor->proveedor = strtoupper($proveedor->proveedor);
        });
    }

    protected $fillable = [
        'proveedor',
        'nit',
        'email',
        'telefono',
        'direccion',
        'departamento_id',
        'municipio_id',
        'contacto',
        'estado_id',
        'user_create_id',
        'user_update_id'
    ];

    // Relación con el estado
    public function estado()
    {
        return $this->belongsTo(\App\Models\ParametroTema::class, 'estado_id');
    }

    // Relación con el departamento
    public function departamento()
    {
        return $this->belongsTo(\App\Models\Departamento::class);
    }

    // Relación con el municipio
    public function municipio()
    {
        return $this->belongsTo(\App\Models\Municipio::class);
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