<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use HasFactory;
    protected $fillable = ['sede', 'direccion', 'user_create_id', 'user_edit_id', 'status', 'municipio_id', 'regional_id'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($sede) {
            $sede->sede = strtoupper($sede->sede);
        });
    }

    public function userCreated()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userEdited()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
}
