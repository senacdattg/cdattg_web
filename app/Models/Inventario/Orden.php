<?php

namespace App\Models\Inventario;

use App\Models\ParametroTema;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Orden extends Model
{
    use HasFactory;

    protected $table = 'ordenes';

    protected $fillable = [
        'descripcion_orden',
        'tipo_orden_id',
        'fecha_devolucion',
        'user_create_id',
        'user_update_id'
    ];

    public function tipoOrden()
    {
        return $this->belongsTo(ParametroTema::class, 'tipo_orden_id');
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
