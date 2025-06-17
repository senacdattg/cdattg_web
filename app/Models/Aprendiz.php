<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Aprendiz extends Model
{
    protected $table = 'aprendices';

    protected $fillable = [
        'persona_id',
    ];

    /**
     * Get the persona that owns the Aprendiz.
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
}
