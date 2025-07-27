<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EvidenciaGuiaAprendizaje extends Model
{

    use HasFactory;

    protected $table = 'evidencia_guia_aprendizaje';

    protected $fillable = ['evidencia_id', 'guia_aprendizaje_id', 'user_create_id', 'user_edit_id'];
}
