<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuiasAprendizaje extends Model
{
    protected $table = 'guia_aprendizajes';
    protected $fillable = [
        'id',
        'codigo',
        'nombre',
        'user_create_id',
        'user_edit_id',
    ];

    public function aprendizaje()
    {
        return $this->belongsTo(Aprendizaje::class);
    }
}
