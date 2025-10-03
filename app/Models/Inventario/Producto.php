<?php

namespace App\Models\Inventario;

use App\Models\Ambiente;
use App\Models\ParametroTema;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

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

    protected $dates = [
        'fecha_vencimiento'
    ];

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
    
    public function creador()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'user_update_id');
    }

    // Nuevas relaciones
    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function marca()
    {
        return $this->belongsTo(Marca::class);
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
}
