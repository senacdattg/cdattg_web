<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tema extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'user_create_id', 'user_edit_id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tema) {
            $tema->status = $tema->status ?? true;
        });

        static::saving(function ($tema) {
            $tema->name = strtoupper($tema->name);
        });
    }

    public function userCreate()
    {
        return  $this->belongsTo(User::class, 'user_create_id');
    }

    public function userUpdate()
    {
        return  $this->belongsTo(User::class, 'user_edit_id');
    }

    public function parametros()
    {
        return $this->belongsToMany(Parametro::class, 'parametros_temas')
            ->withPivot('user_create_id', 'user_edit_id', 'status')
            ->withTimestamps();
    }
}