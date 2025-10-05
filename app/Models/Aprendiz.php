<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Traits\HasRoles;

class Aprendiz extends Model
{
    use HasFactory, SoftDeletes, HasRoles;

    protected $table = 'aprendices';

    /**
     * Los atributos asignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'persona_id',
        'ficha_caracterizacion_id',
        'estado',
        'user_create_id',
        'user_edit_id',
    ];

    /**
     * Los atributos que deben ser casteados.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'estado' => 'boolean',
    ];

    /**
     * Relación con Persona (Many-to-One).
     * Un aprendiz pertenece a una persona.
     *
     * @return BelongsTo
     */
    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    /**
     * Relación con FichaCaracterizacion (Many-to-One).
     * Un aprendiz pertenece a una única ficha de caracterización.
     *
     * @return BelongsTo
     */
    public function fichaCaracterizacion(): BelongsTo
    {
        return $this->belongsTo(FichaCaracterizacion::class, 'ficha_caracterizacion_id');
    }

    /**
     * Relación Many-to-Many con FichaCaracterizacion a través de la tabla pivot.
     * Un aprendiz puede estar asignado a múltiples fichas de caracterización.
     *
     * @return BelongsToMany
     */
    public function fichas(): BelongsToMany
    {
        return $this->belongsToMany(
            FichaCaracterizacion::class,
            'aprendiz_fichas_caracterizacion',
            'aprendiz_id',
            'ficha_id'
        )->withTimestamps();
    }

    /**
     * Relación con AprendizFicha (One-to-Many).
     * Un aprendiz tiene un registro en la tabla intermedia para asistencias.
     *
     * @return HasMany
     */
    public function aprendizFichas(): HasMany
    {
        return $this->hasMany(AprendizFicha::class, 'aprendiz_id');
    }

    /**
     * Relación con AsistenciaAprendiz a través de AprendizFicha.
     * Un aprendiz tiene múltiples asistencias.
     *
     * @return HasManyThrough
     */
    public function asistencias(): HasManyThrough
    {
        return $this->hasManyThrough(
            AsistenciaAprendiz::class,
            AprendizFicha::class,
            'aprendiz_id',        // FK en aprendiz_fichas_caracterizacion
            'aprendiz_ficha_id',  // FK en asistencia_aprendices
            'id',                 // PK en aprendices
            'id'                  // PK en aprendiz_fichas_caracterizacion
        );
    }

    /**
     * Relación con el usuario que creó el registro.
     *
     * @return BelongsTo
     */
    public function userCreatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    /**
     * Relación con el usuario que editó el registro por última vez.
     *
     * @return BelongsTo
     */
    public function userUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    /**
     * Obtiene el usuario asociado a través de la persona.
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'persona_id', 'persona_id')
            ->through('persona');
    }

    /**
     * Verifica si el aprendiz tiene un rol específico.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        // Primero verifica si tiene el rol directamente asignado
        if (parent::hasRole($role)) {
            return true;
        }

        // Si no, verifica a través del usuario asociado
        if ($this->persona && $this->persona->user) {
            return $this->persona->user->hasRole($role);
        }

        return false;
    }

    /**
     * Verifica si el aprendiz tiene un permiso específico.
     *
     * @param string $permission
     * @return bool
     */
    public function hasPermissionTo(string $permission): bool
    {
        // Primero verifica si tiene el permiso directamente asignado
        if (parent::hasPermissionTo($permission)) {
            return true;
        }

        // Si no, verifica a través del usuario asociado
        if ($this->persona && $this->persona->user) {
            return $this->persona->user->hasPermissionTo($permission);
        }

        return false;
    }

    /**
     * Obtiene todos los roles del aprendiz (directos y a través del usuario).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllRoles()
    {
        $roles = collect();

        // Roles directos del aprendiz
        $roles = $roles->merge($this->roles);

        // Roles a través del usuario asociado
        if ($this->persona && $this->persona->user) {
            $roles = $roles->merge($this->persona->user->roles);
        }

        return $roles->unique('id');
    }

    /**
     * Obtiene todos los permisos del aprendiz (directos y a través del usuario).
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAllPermissions()
    {
        $permissions = collect();

        // Permisos directos del aprendiz
        $permissions = $permissions->merge($this->permissions);

        // Permisos a través del usuario asociado
        if ($this->persona && $this->persona->user) {
            $permissions = $permissions->merge($this->persona->user->permissions);
        }

        return $permissions->unique('id');
    }
}
