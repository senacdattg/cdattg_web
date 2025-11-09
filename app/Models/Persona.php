<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Sanctum\HasApiTokens;
use App\Models\PersonaContactAlert;
use App\Models\FichaCaracterizacion;

class Persona extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Los atributos asignables.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'fecha_nacimiento',
        'genero',
        'telefono',
        'celular',
        'email',
        'pais_id',
        'departamento_id',
        'municipio_id',
        'direccion',
        'status',
        'estado_sofia',
        'condocumento',
        'user_create_id',
        'user_edit_id',
        'parametro_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($persona) {
            $persona->primer_nombre = strtoupper($persona->primer_nombre);
            $persona->segundo_nombre = strtoupper($persona->segundo_nombre);
            $persona->primer_apellido = strtoupper($persona->primer_apellido);
            $persona->segundo_apellido = strtoupper($persona->segundo_apellido);
            $persona->direccion = strtoupper($persona->direccion);
        });
    }

    public function user()
    {
        return $this->hasOne(User::class, 'persona_id');
    }

    public function tipoDocumento()
    {
        return $this->belongsTo(Parametro::class, 'tipo_documento');
    }

    public function tipoGenero()
    {
        return $this->belongsTo(Parametro::class, 'genero');
    }

    public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function caracterizacionProgramas()
    {
        return $this->hasMany(FichaCaracterizacion::class, 'instructor_id');
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    /**
     * Relación One-to-One con Aprendiz.
     * Una persona puede ser un aprendiz.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function aprendiz()
    {
        return $this->hasOne(Aprendiz::class, 'persona_id');
    }

    /**
     * Verifica si la persona es un aprendiz.
     *
     * @return bool
     */
    public function esAprendiz(): bool
    {
        return $this->aprendiz()->exists();
    }

    /**
     * Verifica si la persona es un aprendiz activo.
     *
     * @return bool
     */
    public function esAprendizActivo(): bool
    {
        return $this->aprendiz()->where('estado', 1)->exists();
    }

    /**
     * Verifica si la persona tiene un rol específico.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        if (!$this->user) {
            return false;
        }

        return $this->user->hasRole(strtoupper($role));
    }

    /**
     * Verifica si la persona es instructor.
     *
     * @return bool
     */
    public function esInstructor(): bool
    {
        return $this->instructor()->exists();
    }


    /**
     * Verifica si la persona tiene el rol de APRENDIZ.
     *
     * @return bool
     */
    public function tieneRolAprendiz(): bool
    {
        if (!$this->user) {
            return false;
        }

        return $this->user->hasRole('APRENDIZ');
    }

    /**
     * Accesor para obtener el nombre completo de la persona.
     *
     * @return string
     */
    public function getNombreCompletoAttribute()
    {
        // Usa array_filter para omitir valores vacíos y join para unirlos con espacios
        $nombres = [
            $this->primer_nombre,
            $this->segundo_nombre,
            $this->primer_apellido,
            $this->segundo_apellido
        ];
        return trim(implode(' ', array_filter($nombres)));
    }

    /**
     * Accesor para calcular la edad a partir de la fecha de nacimiento.
     *
     * @return int
     */
    public function getEdadAttribute()
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }

    /**
     * Accesor para convertir el email a mayúsculas.
     *
     * @param string $value
     * @return string
     */
    public function getEmailAttribute($value)
    {
        return strtoupper($value);
    }

    /**
     * Accesor para obtener la etiqueta del estado de SenaSofiaPlus.
     *
     * @return string
     */
    public function getEstadoSofiaLabelAttribute()
    {
        return match ($this->estado_sofia) {
            0 => 'No registrado',
            1 => 'Registrado',
            2 => 'Requiere cambio de cédula',
            default => 'Desconocido'
        };
    }

    /**
     * Accesor para obtener la clase CSS del badge del estado de SenaSofiaPlus.
     *
     * @return string
     */
    public function getEstadoSofiaBadgeClassAttribute()
    {
        return match ($this->estado_sofia) {
            0 => 'bg-danger',
            1 => 'bg-success',
            2 => 'bg-warning',
            default => 'bg-dark'
        };
    }

    public function userCreatedBy()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }

    public function userUpdatedBy()
    {
        return $this->belongsTo(User::class, 'user_edit_id');
    }

    /**
     * Relación con el parámetro de caracterización.
     */
    public function parametroCaracterizacion()
    {
        return $this->belongsTo(Parametro::class, 'parametro_id');
    }

    public function caracterizacionesComplementarias(): BelongsToMany
    {
        return $this->belongsToMany(
            Parametro::class,
            'persona_caracterizacion',
            'persona_id',
            'parametro_id'
        )->withTimestamps();
    }

    public function getCaracterizacionesComplementariasNombresAttribute(): array
    {
        return $this->caracterizacionesComplementarias
            ->pluck('nombre')
            ->filter()
            ->values()
            ->all();
    }

    public function getCaracterizacionesComplementariasTextoAttribute(): string
    {
        $nombres = $this->caracterizaciones_complementarias_nombres;

        return $nombres ? implode(', ', $nombres) : '';
    }

    public function contactAlerts()
    {
        return $this->hasMany(PersonaContactAlert::class);
    }
}
