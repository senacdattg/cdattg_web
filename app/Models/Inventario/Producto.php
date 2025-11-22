<?php

namespace App\Models\Inventario;

use App\Models\Parametro;
use App\Traits\Seguimiento;
use App\Models\Ambiente;
use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    use HasFactory, Seguimiento;

    protected $table = 'productos';

    protected $guarded = [];

    protected $casts = [
        'fecha_vencimiento' => 'datetime'
    ];

    protected static function booted() : void
    {
        static::creating(function ($producto) {
            if (isset($producto->producto)) {
                $producto->producto = strtoupper($producto->producto);
            }
        });

        static::updating(function ($producto) {
            if (isset($producto->producto)) {
                $producto->producto = strtoupper($producto->producto);
            }
        });
    }

    // Relaciones existentes
    public function tipoProducto() : BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'tipo_producto_id');
    }

    public function unidadMedida() : BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'unidad_medida_id');
    }

    public function estado() : BelongsTo
    {
        return $this->belongsTo(ParametroTema::class, 'estado_producto_id');
    }

    public function marca() : BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'marca_id')
            ->whereHas('temas', function($query) {
                $query->where('name', 'MARCAS');
            });
    }

    public function categoria() : BelongsTo
    {
        return $this->belongsTo(Parametro::class, 'categoria_id')
        ->whereHas('temas', function($query) {
            $query->where('name', 'CATEGORIAS');
        });
    }

    public function contratoConvenio() : BelongsTo
    {
        return $this->belongsTo(ContratoConvenio::class);
    }

    public function ambiente() : BelongsTo
    {
        return $this->belongsTo(Ambiente::class);
    }

    public function proveedor() : BelongsTo
    {
        return $this->belongsTo(Proveedor::class);
    }
    
    // Relación con detalles de órdenes
    public function detalleOrdenes() : HasMany
    {
        return $this->hasMany(DetalleOrden::class, 'producto_id');
    }


    // Verificar si hay stock disponible
    public function tieneStockDisponible(int $cantidadRequerida) : bool
    {
        return $this->cantidad >= $cantidadRequerida;
    }


    // Descontar stock del producto
    public function descontarStock(int $cantidad) : self
    {
        if (!$this->tieneStockDisponible($cantidad)) {
            throw new \Exception("Stock insuficiente. Disponible: {$this->cantidad}, Requerido: {$cantidad}");
        }

        $this->cantidad -= $cantidad;
        $this->save();

        return $this;
    }

   
    // Devolver stock al producto
    public function devolverStock(int $cantidad) : self
    {
        $this->cantidad += $cantidad;
        $this->save();

        return $this;
    }

    public function esConsumible(): bool
    {
        $this->loadMissing(['tipoProducto.parametro']);

        $tipo = $this->tipoProducto;
        if ($tipo === null || $tipo->parametro === null) {
            return false;
        }

        return strtoupper($tipo->parametro->name) === 'CONSUMIBLE';
    }

    // Obtener el porcentaje de stock actual
    public function getPorcentajeStock(int $stockMaximo = 100) : float
    {
        if ($this->cantidad <= 0) {
            return 0;
        }
        return round(($this->cantidad / $stockMaximo) * 100, 2);
    }

 
    // Obtener estado del stock (crítico, bajo, medio, normal)
    public function getEstadoStock() : string
    {
        $cantidad = $this->cantidad;

        if ($cantidad <= 5) {
            return 'critico';
        } elseif ($cantidad <= 10) {
            return 'bajo';
        } elseif ($cantidad <= 20) {
            return 'medio';
        }

        return 'normal';
    }

    // Obtener badge HTML para mostrar estado de stock
    public function getBadgeStock() : string
    {
        $estado = $this->getEstadoStock();
        $clases = [
            'critico' => 'badge-danger',
            'bajo' => 'badge-warning',
            'medio' => 'badge-info',
            'normal' => 'badge-success'
        ];

        $textos = [
            'critico' => 'CRÍTICO',
            'bajo' => 'BAJO',
            'medio' => 'MEDIO',
            'normal' => 'NORMAL'
        ];

        $clase = $clases[$estado] ?? 'badge-secondary';
        $texto = $textos[$estado] ?? 'N/A';

        return "<span class='badge {$clase}'>{$texto}: {$this->cantidad}</span>";
    }
}
