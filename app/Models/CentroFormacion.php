<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentroFormacion extends Model
{
    
    protected $fillable = ['nombre', 'regional_id', 'telefono', 'direccion', 'web', 'status', 'user_create_id', 'user_update_id'];

    public function regional()
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }

    public function userCreated()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_create_id');
    }

    public function userEdited()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_update_id');
    }
}
