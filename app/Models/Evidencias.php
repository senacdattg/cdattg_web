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
        'user_create_id',
        'user_edit_id',
    ];
}
