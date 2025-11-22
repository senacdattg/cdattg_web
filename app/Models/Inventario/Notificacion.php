<?php

namespace App\Models\Inventario;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Carbon;

class Notificacion extends DatabaseNotification
{
    protected $table = 'notificaciones';

    public $timestamps = true;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $guarded = [];

    protected $casts = [
        'datos' => 'array',
        'leida_en' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Laravel espera 'data' pero nuestra columna es 'datos'
     */
    public function getDataAttribute() : array
    {
        return $this->attributes['datos'] ?? [];
    }

    /**
     * Laravel espera 'type' pero nuestra columna es 'tipo'
     */
    public function getTypeAttribute() : ?string
    {
        return $this->attributes['tipo'] ?? null;
    }

    /**
     * Laravel espera 'read_at' pero nuestra columna es 'leida_en'
     */
    public function getReadAtAttribute() : ?Carbon
    {
        return $this->leida_en;
    }

    /**
     * Marcar la notificación como leída
     */
    public function markAsRead() : void
    {
        if (is_null($this->leida_en)) {
            $this->forceFill(['leida_en' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Marcar la notificación como no leída
     */
    public function markAsUnread() : void
    {
        if (!is_null($this->leida_en)) {
            $this->forceFill(['leida_en' => null])->save();
        }
    }

    /**
     * Determinar si la notificación ha sido leída
     */
    public function read() : bool
    {
        return $this->leida_en !== null;
    }

    /**
     * Determinar si la notificación no ha sido leída
     */
    public function unread() : bool
    {
        return $this->leida_en === null;
    }
}
