<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametroTema extends Model
{

    protected $table = 'parametros_temas';

    protected $fillable = [
        'parametro_id',
        'tema_id',
        'status',
        'user_create_id',
        'user_edit_id',
    ];

    public function parametro()
    {
        return $this->belongsTo(Parametro::class);
    }

    public function tema()
    {
        return $this->belongsTo(Tema::class);
    }
}
