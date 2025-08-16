<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidencias extends Model
{
    protected $table = 'evidencias';
    protected $fillable = [
        'codigo',
        'nombre',
        'id_estado',
        'fecha_evidencia',
        'user_create_id',
        'user_edit_id',
    ];

    public function ambiente()
    {
        return $this->belongsTo(Ambientes::class, 'id_ambiente');
    }

    /**
     * Relación muchos a muchos con GuiasAprendizaje a través de la tabla intermedia
     */
    public function guiasAprendizaje()
    {
        return $this->belongsToMany(GuiasAprendizaje::class, 'evidencia_guia_aprendizaje', 'evidencia_id', 'guia_aprendizaje_id')
                    ->withPivot('user_create_id', 'user_edit_id')
                    ->withTimestamps();
    }

    static function terminarActividad($id)
    {
        $actividad = Evidencias::find($id);
        $actividad->id_estado = 27;
        $actividad->save();
    }
}
