<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonaContactAlert extends Model
{
    use HasFactory;

    protected $fillable = [
        'persona_id',
        'persona_import_id',
        'missing_email',
        'missing_celular',
        'missing_telefono',
        'observaciones',
        'raw_payload',
    ];

    protected $casts = [
        'missing_email' => 'boolean',
        'missing_celular' => 'boolean',
        'missing_telefono' => 'boolean',
        'raw_payload' => 'array',
    ];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function import(): BelongsTo
    {
        return $this->belongsTo(PersonaImport::class, 'persona_import_id');
    }
}
