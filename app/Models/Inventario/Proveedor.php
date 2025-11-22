<?php

namespace App\Models\Inventario;

use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proveedor extends Model
{
    use HasFactory;
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

    protected $guarded = [];

    // Relación con el estado
    public function estado() : BelongsTo
    {
        return $this->belongsTo(\App\Models\ParametroTema::class, 'estado_id');
    }

    // Relación con el departamento
    public function departamento() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Departamento::class);
    }

    // Relación con el municipio
    public function municipio() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Municipio::class);
    }

    // Relación con contratos y convenios
    public function contratosConvenios() : HasMany
    {
        return $this->hasMany(ContratoConvenio::class);
    }

    // Relación con productos
    public function productos() : HasMany
    {
        return $this->hasMany(Producto::class);
    }
}