<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuiasResultados extends Model
{
    protected $table = 'guia_aprendizaje_rap';
    protected $fillable = [
        'id',
        'guia_aprendizaje_id',
        'rap_id',
        'user_create_id',
        'user_edit_id',
    ];

    public function guia_aprendizaje()
    {
        return $this->belongsTo(GuiaAprendizaje::class);
    }

    public function rap()
    {
        return $this->belongsTo(Rap::class);
    }
}
