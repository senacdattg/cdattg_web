<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\GuiasAprendizaje;
use App\Models\ResultadosAprendizaje;


class GuiaAprendizajeRap extends Model
{
    protected $table = 'guia_aprendizaje_rap';
    protected $fillable = [
        'guia_aprendizaje_id',
        'rap_id',
        'user_create_id',
        'user_edit_id',
    ];

    public function guiaAprendizaje()
    {
        return $this->belongsTo(GuiasAprendizaje::class, 'guia_aprendizaje_id');
    }

    public function rap()
    {
        return $this->belongsTo(ResultadosAprendizaje::class, 'rap_id', 'id');
    }
}
