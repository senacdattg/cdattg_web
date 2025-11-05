<?php

namespace App\Models\Inventario;

use App\Traits\Seguimiento;
use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Model;

class ContratoConvenio extends Model
{
    use Seguimiento;

    protected $table = 'contratos_convenios';

    protected static function booted()
    {
        static::creating(function ($contrato) {
            $contrato->name = strtoupper($contrato->name);
        });

        static::updating(function ($contrato) {
            $contrato->name = strtoupper($contrato->name);
        });
    }

    protected $fillable = [
        'name',
        'codigo',
        'proveedor_id',
        'fecha_inicio',
        'fecha_fin',
        'estado_id',
        'user_create_id',
        'user_update_id'
    ];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin'
    ];

    // Relación con el proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    // Relación con el estado
    public function estado()
    {
        return $this->belongsTo(ParametroTema::class, 'estado_id');
    }

    // Relación con productos
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }
}