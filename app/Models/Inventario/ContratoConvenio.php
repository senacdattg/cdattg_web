<?php

namespace App\Models\Inventario;

use App\Models\ParametroTema;
use App\Traits\Seguimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContratoConvenio extends Model
{
    use HasFactory;
    use Seguimiento;

    protected $table = 'contratos_convenios';

    protected static function booted() : void
    {
        static::creating(function ($contrato) {
            $contrato->name = strtoupper($contrato->name);
        });

        static::updating(function ($contrato) {
            $contrato->name = strtoupper($contrato->name);
        });
    }

    protected $guarded= [];

    protected $dates = [
        'fecha_inicio',
        'fecha_fin'
    ];

    // Relación con el proveedor
    public function proveedor() : BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }

    // Relación con el estado
    public function estado() : BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'estado_id');
    }

    // Relación con productos
    public function productos() : HasMany
    {
        return $this->hasMany(Producto::class);
    }
}