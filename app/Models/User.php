<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

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
