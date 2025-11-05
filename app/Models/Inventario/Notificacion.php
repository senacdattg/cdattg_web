<?php

namespace App\Models\Inventario;

use Illuminate\Notifications\DatabaseNotification;

class Notificacion extends DatabaseNotification
{
    /**
     * La tabla asociada con el modelo.
     *
     * @var string
     */
    protected $table = 'notificaciones';

    /**
     * Indica si el modelo debe tener timestamps.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Indica si el IDs son auto-incrementales.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * El tipo de dato de la clave primaria ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'tipo',
        'notificable_type',
        'notificable_id',
        'datos',
        'leida_en',
    ];

    /**
     * Los atributos que deben ser convertidos.
     *
     * @var array
     */
    protected $casts = [
        'datos' => 'array',
        'leida_en' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Laravel espera 'data' pero nuestra columna es 'datos'
     */
    public function getDataAttribute()
    {
        return $this->attributes['datos'] ?? [];
    }

    /**
     * Laravel espera 'type' pero nuestra columna es 'tipo'
     */
    public function getTypeAttribute()
    {
        return $this->attributes['tipo'] ?? null;
    }

    /**
     * Laravel espera 'read_at' pero nuestra columna es 'leida_en'
     */
    public function getReadAtAttribute()
    {
        return $this->leida_en;
    }

    /**
     * Marcar la notificación como leída
     */
    public function markAsRead()
    {
        if (is_null($this->leida_en)) {
            $this->forceFill(['leida_en' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Marcar la notificación como no leída
     */
    public function markAsUnread()
    {
        if (!is_null($this->leida_en)) {
            $this->forceFill(['leida_en' => null])->save();
        }
    }

    /**
     * Determinar si la notificación ha sido leída
     */
    public function read()
    {
        return $this->leida_en !== null;
    }

    /**
     * Determinar si la notificación no ha sido leída
     */
    public function unread()
    {
        return $this->leida_en === null;
    }
}
