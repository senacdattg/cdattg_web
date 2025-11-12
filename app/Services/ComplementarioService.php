<?php

namespace App\Services;

use App\Models\Ambiente;
use App\Models\AspiranteComplementario;
use App\Models\ComplementarioOfertado;
use App\Models\JornadaFormacion;
use App\Models\ParametroTema;
use App\Repositories\TemaRepository;
use Illuminate\Database\Eloquent\Collection;

class ComplementarioService
{
    public function __construct(
        private readonly TemaRepository $temaRepository
    ) {}
    /**
     * Obtener icono para un programa complementario
     */
    public function getIconoForPrograma($nombre)
    {
        $iconos = [
            'Auxiliar de Cocina' => 'fas fa-utensils',
            'Acabados en Madera' => 'fas fa-hammer',
            'Confección de Prendas' => 'fas fa-cut',
            'Mecánica Básica Automotriz' => 'fas fa-car',
            'Cultivos de Huertas Urbanas' => 'fas fa-spa',
            'Normatividad Laboral' => 'fas fa-gavel',
        ];

        return $iconos[$nombre] ?? 'fas fa-graduation-cap';
    }

    /**
     * Obtener clase CSS para el badge según el estado del programa
     */
    public function getBadgeClassForEstado($estado)
    {
        $badgeClasses = [
            0 => 'bg-secondary', // Sin Oferta
            1 => 'bg-success',   // Con Oferta
            2 => 'bg-warning',   // Cupos Llenos
        ];

        return $badgeClasses[$estado] ?? 'bg-secondary';
    }

    /**
     * Obtener label del estado del programa
     */
    public function getEstadoLabel($estado)
    {
        $estados = [
            0 => 'Sin Oferta',
            1 => 'Con Oferta',
            2 => 'Cupos Llenos',
        ];

        return $estados[$estado] ?? 'Desconocido';
    }

    /**
     * Enriquecer un programa con datos auxiliares para la vista.
     */
    public function enriquecerPrograma(ComplementarioOfertado $programa): ComplementarioOfertado
    {
        $programa->icono = $this->getIconoForPrograma($programa->nombre);
        $programa->badge_class = $this->getBadgeClassForEstado($programa->estado);
        $programa->estado_label = $this->getEstadoLabel($programa->estado);
        $programa->modalidad_nombre = $programa->modalidad->parametro->name ?? null;
        $programa->jornada_nombre = $programa->jornada->jornada ?? null;

        return $programa;
    }

    /**
     * Enriquecer una colección de programas complementarios.
     */
    public function enriquecerProgramas(Collection $programas): Collection
    {
        return $programas->map(function (ComplementarioOfertado $programa) {
            return $this->enriquecerPrograma($programa);
        });
    }

    /**
     * Obtener programas con relaciones necesarias para diferentes vistas.
     */
    public function obtenerProgramas(array $relations = [], ?int $estado = null): Collection
    {
        $query = ComplementarioOfertado::query()->with($relations);

        if (!is_null($estado)) {
            $query->where('estado', $estado);
        }

        return $query->get();
    }

    /**
     * Sincronizar días de formación garantizando atomicidad.
     */
    public function sincronizarDiasFormacion(ComplementarioOfertado $programa, ?array $dias): void
    {
        if (empty($dias)) {
            $programa->diasFormacion()->detach();
            return;
        }

        $programa->diasFormacion()->sync(
            collect($dias)->mapWithKeys(static function ($dia) {
                return [
                    $dia['dia_id'] => [
                        'hora_inicio' => $dia['hora_inicio'],
                        'hora_fin' => $dia['hora_fin'],
                    ],
                ];
            })->all()
        );
    }

    /**
     * Datos compartidos para vistas de gestión/admin.
     */
    public function obtenerDatosFormulario(): array
    {
        $modalidades = ParametroTema::query()
            ->where('tema_id', 5)
            ->with('parametro')
            ->get();

        $jornadas = JornadaFormacion::query()->get();

        $ambientes = Ambiente::query()
            ->with('piso')
            ->where('status', 1)
            ->orderBy('piso_id')
            ->orderBy('title')
            ->get();

        return compact('modalidades', 'jornadas', 'ambientes');
    }

    /**
     * Obtener tipos de documento dinámicamente desde el tema-parametro
     */
    public function getTiposDocumento()
    {
        $temaTipoDocumento = $this->temaRepository->obtenerTiposDocumento();

        if (!$temaTipoDocumento) {
            return collect();
        }

        return $temaTipoDocumento->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }

    /**
     * Obtener géneros dinámicamente desde el tema-parametro
     */
    public function getGeneros()
    {
        $temaGenero = $this->temaRepository->obtenerGeneros();

        if (!$temaGenero) {
            return collect();
        }

        return $temaGenero->parametros()
            ->where('parametros_temas.status', 1)
            ->orderBy('parametros.name')
            ->get(['parametros.id', 'parametros.name']);
    }

    /**
     * Verificar si un usuario ya está inscrito en un programa
     */
    public function verificarInscripcionExistente($personaId, $programaId)
    {
        return AspiranteComplementario::where('persona_id', $personaId)
            ->where('complementario_id', $programaId)
            ->exists();
    }

    /**
     * Crear aspirante complementario
     */
    public function crearAspirante($personaId, $programaId, $observaciones = null)
    {
        return AspiranteComplementario::create([
            'persona_id' => $personaId,
            'complementario_id' => $programaId,
            'observaciones' => $observaciones,
            'estado' => 1, // Estado "En proceso"
        ]);
    }

    /**
     * Actualizar estado del aspirante
     */
    public function actualizarEstadoAspirante($aspiranteId, $estado)
    {
        $aspirante = AspiranteComplementario::findOrFail($aspiranteId);
        $aspirante->update(['estado' => $estado]);
        return $aspirante;
    }

    /**
     * Obtener estadísticas básicas de un programa
     */
    public function obtenerEstadisticasPrograma($programaId)
    {
        $programa = ComplementarioOfertado::findOrFail($programaId);

        return [
            'total_aspirantes' => AspiranteComplementario::where('complementario_id', $programaId)->count(),
            'aspirantes_activos' => AspiranteComplementario::where('complementario_id', $programaId)
                ->where('estado', 1)->count(),
            'aspirantes_aceptados' => AspiranteComplementario::where('complementario_id', $programaId)
                ->where('estado', 3)->count(),
            'cupos_disponibles' => $programa->cupos
                - AspiranteComplementario::where('complementario_id', $programaId)->count(),
        ];
    }
}
