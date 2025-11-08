<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PersonaImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_name',
        'disk',
        'path',
        'total_rows',
        'processed_rows',
        'success_count',
        'duplicate_count',
        'missing_contact_count',
        'status',
        'error_message',
    ];

    protected $casts = [
        'total_rows' => 'integer',
        'processed_rows' => 'integer',
        'success_count' => 'integer',
        'duplicate_count' => 'integer',
        'missing_contact_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function issues(): HasMany
    {
        return $this->hasMany(PersonaImportIssue::class);
    }

    public function contactAlerts(): HasMany
    {
        return $this->hasMany(PersonaContactAlert::class);
    }
}
