<?php

namespace App\Models\Inventario;

use App\Models\Parametro;
use App\Traits\Seguimiento;
use App\Models\Ambiente;
use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory, Seguimiento;

    protected $table = 'productos';

    protected $fillable = [
        'producto',
        'tipo_producto_id',
        'descripcion',
        'peso',
        'unidad_medida_id',
        'cantidad',
        'codigo_barras',
        'estado_producto_id',
        'categoria_id',
        'marca_id',
        'contrato_convenio_id',
        'ambiente_id',
        'proveedor_id',
        'fecha_vencimiento',
        'imagen',
        'user_create_id',
        'user_update_id'
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime'
    ];

    protected static function booted()
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
    public function tipoProducto()
    {
        return $this->belongsTo(ParametroTema::class, 'tipo_producto_id');
    }

    public function unidadMedida()
    {
        return $this->belongsTo(ParametroTema::class, 'unidad_medida_id');
    }

    public function estado()
    {
        return $this->belongsTo(ParametroTema::class, 'estado_producto_id');
    }

    public function marca()
    {
        return $this->belongsTo(Parametro::class, 'marca_id')
            ->whereHas('temas', function($query) {
                $query->where('name', 'MARCAS');
            });
    }

    public function categoria()
    {
        return $this->belongsTo(Parametro::class, 'categoria_id')
        ->whereHas('temas', function($query) {
            $query->where('name', 'CATEGORIAS');
        });
    }

    public function contratoConvenio()
    {
        return $this->belongsTo(ContratoConvenio::class);
    }

    public function ambiente()
    {
        return $this->belongsTo(Ambiente::class);
    }

    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }
    
    // Relación con detalles de órdenes
    public function detalleOrdenes()
    {
        return $this->hasMany(DetalleOrden::class, 'productos_id');
    }

  
    // Verificar si hay stock disponible
    public function StockDisponible($cantidadRequerida)
    {
        return $this->cantidad >= $cantidadRequerida;
    }


    // Descontar stock del producto
    public function descontarStock($cantidad)
    {
        if (!$this->StockDisponible($cantidad)) {
            throw new \Exception("Stock insuficiente. Disponible: {$this->cantidad}, Requerido: {$cantidad}");
        }

        $this->cantidad -= $cantidad;
        $this->save();

        return $this;
    }

   
    // Devolver stock al producto
    public function devolverStock($cantidad)
    {
        $this->cantidad += $cantidad;
        $this->save();

        return $this;
    }

    // Obtener el porcentaje de stock actual
    public function getPorcentajeStock($stockMaximo = 100)
    {
        if ($this->cantidad <= 0) {
            return 0;
        }
        return round(($this->cantidad / $stockMaximo) * 100, 2);
    }

 
    // Obtener estado del stock (crítico, bajo, medio, normal)
    public function getEstadoStock()
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
    public function getBadgeStock()
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
