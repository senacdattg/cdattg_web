<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ambiente extends Model
{
    use HasFactory;
    protected $fillable = ['title','piso_id', 'user_create_id', 'user_edit_id', 'status'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($ambiente) {
            $ambiente->title = strtoupper($ambiente->title);
        });
    }

    public function piso()
    {
        return $this->belongsTo(Piso::class, 'piso_id');
    }

    public function userCreate()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userEdit()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }
    public function fichaCaracterizacion()
    {
        return $this->hasMany(FichaCaracterizacion::class, 'ambiente_id');
    }

    public function complementariosOfertados()
    {
        return $this->hasMany(ComplementarioOfertado::class, 'ambiente_id');
    }

    // Accessor para obtener la sede a través de la cadena de relaciones
    public function getSedeAttribute()
    {
        return $this->piso?->bloque?->sede;
    }
}
