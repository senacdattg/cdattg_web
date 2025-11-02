<?php

namespace App\Models\Inventario;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'read_at',
    ];
}
