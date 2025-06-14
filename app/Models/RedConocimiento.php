<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RedConocimiento extends Model
{
    /** @use HasFactory<\Database\Factories\RedConocimientoFactory> */
    protected $fillable = ['nombre', 'regionals_id'];
    use HasFactory;
}
