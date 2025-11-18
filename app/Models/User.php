<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Inventario\Notificacion;

/**
 * Modelo User
 *
 * @method bool hasVerifiedEmail() Determina si el usuario ha verificado su correo electrónico
 * @method bool markEmailAsVerified() Marca el correo electrónico del usuario como verificado
 * @method void sendEmailVerificationNotification() Envía la notificación de verificación de correo
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, MustVerifyEmailTrait;

    /**
     * Especificar la tabla de notificaciones personalizada
     */
    public function notifications()
    {
        return $this->morphMany(Notificacion::class, 'notificable', 'notificable_type', 'notificable_id')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Obtener las notificaciones leídas de la entidad.
     */
    public function readNotifications()
    {
        return $this->notifications()->whereNotNull('leida_en');
    }

    /**
     * Obtener las notificaciones no leídas de la entidad.
     */
    public function unreadNotifications()
    {
        return $this->notifications()->whereNull('leida_en');
    }

    protected $fillable = [
        'email',
        'password',
        'status',
        'persona_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Boot del modelo para sincronizar email con persona
     */
    protected static function boot()
    {
        parent::boot();

        // Sincronizar email con persona al crear o actualizar
        static::saving(function ($user) {
            // Si el email cambió y hay una persona relacionada, sincronizar
            if ($user->isDirty('email') && $user->persona_id) {
                // Usar DB directo para evitar loops infinitos
                DB::table('personas')
                    ->where('id', $user->persona_id)
                    ->update(['email' => $user->email]);
            }
        });
    }

    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    // accesor para obtener el nombre completo del usuario
    public function getNameAttribute()
    {
        if ($this->persona) {
            $nombre = trim($this->persona->primer_nombre . ' ' . $this->persona->segundo_nombre);
            $apellido = trim($this->persona->primer_apellido . ' ' . $this->persona->segundo_apellido);
            return trim($nombre . ' ' . $apellido);
        }
        return 'Usuario sin nombre';
    }

    public function entradaSalida()
    {
        return $this->hasMany(EntradaSalida::class);
    }
    
    // relación de bloques y usuario
    public function bloqueCreated()
    {
        return $this->hasMany(Bloque::class, 'user_create_id');
    }
    
    public function bloqueEdited()
    {
        return $this->hasMany(Bloque::class, 'user_edit_id');
    }
    
    // relación de sedes y usuario
    public function sedeCreated()
    {
        return $this->hasMany(Sede::class);
    }
    
    public function sedeEdited()
    {
        return $this->hasMany(Sede::class);
    }
    
    // relacion  entre piso y usuario
    public function pisoCreated()
    {
        return $this->hasMany(Piso::class, 'user_create_id');
    }
    
    public function pisoEdited()
    {
        return $this->hasMany(Piso::class, 'user_edit_id');
    }
    
    // relacion  entre ambiente y usuario
    public function ambienteCreated()
    {
        return $this->hasMany(Ambiente::class, 'user_create_id');
    }
    
    public function ambienteEdited()
    {
        return $this->hasMany(Ambiente::class, 'user_edit_id');
    }
    
    public function parametrosCreated()
    {
        return $this->hasMany(Parametro::class, 'user_create_id');
    }

    // relacion e sedes y usuario
    public function fichaCaracterizacionCreate()
    {
        return $this->hasMany(FichaCaracterizacion::class);
    }
    
    public function fichaCaracerizacionEdit()
    {
        return $this->hasMany(FichaCaracterizacion::class);
    }

}
