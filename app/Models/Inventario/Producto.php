<?php

namespace App\Models\Inventario;

use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

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
        'imagen',
        'user_create_id',
        'user_update_id'
    ];

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
}
