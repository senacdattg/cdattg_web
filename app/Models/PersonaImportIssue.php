<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonaImportIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'persona_import_id',
        'row_number',
        'issue_type',
        'numero_documento',
        'email',
        'celular',
        'raw_payload',
    ];

    protected $casts = [
        'raw_payload' => 'array',
    ];

    public function import(): BelongsTo
    {
        return $this->belongsTo(PersonaImport::class, 'persona_import_id');
    }
}
