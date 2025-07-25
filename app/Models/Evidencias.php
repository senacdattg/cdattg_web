<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidencias extends Model
{
    protected $table = 'evidencias';
    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'hora_entrada',
        'hora_salida',
        'id_estado',
        'id_ambiente',
        'user_create_id',
        'user_edit_id',
    ];

    public function estado()
    {
        return $this->belongsTo(ParametrosTemas::class, 'id_estado');
    }

    public function ambiente()
    {
        return $this->belongsTo(Ambientes::class, 'id_ambiente');
    }
}
