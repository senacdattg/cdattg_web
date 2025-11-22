<?php

namespace App\Services;

use App\Repositories\InstructorRepository;
use App\Models\Instructor;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class InstructorService
{
    protected InstructorRepository $repository;

    public function __construct(InstructorRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Lista instructores con filtros y paginación
     *
     * @param array $filtros
     * @return LengthAwarePaginator
     */
    public function listarConFiltros(array $filtros = []): LengthAwarePaginator
    {
        $query = Instructor::with([
            'persona.tipoDocumento',
            'regional',
            'instructorFichas' => function($q) {
                $q->with('ficha.programaFormacion');
            }
        ]);

        // Filtro de búsqueda
        if (!empty($filtros['search'])) {
            $search = $filtros['search'];
            $query->where(function($q) use ($search) {
                $q->whereHas('persona', function($personaQuery) use ($search) {
                    $personaQuery->where('primer_nombre', 'like', "%{$search}%")
                        ->orWhere('segundo_nombre', 'like', "%{$search}%")
                        ->orWhere('primer_apellido', 'like', "%{$search}%")
                        ->orWhere('segundo_apellido', 'like', "%{$search}%")
                        ->orWhere('numero_documento', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        // Filtro por estado
        if (isset($filtros['estado']) && $filtros['estado'] !== 'todos') {
            $query->where('status', $filtros['estado'] === 'activos');
        }

        // Filtro por especialidad
        if (!empty($filtros['especialidad'])) {
            $query->whereJsonContains('especialidades', $filtros['especialidad']);
        }

        // Filtro por regional
        if (!empty($filtros['regional'])) {
            $query->where('regional_id', $filtros['regional']);
        }

        $perPage = $filtros['per_page'] ?? 15;

        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    /**
     * Obtiene un instructor con sus relaciones
     *
     * @param int $id
     * @return Instructor|null
     */
    public function obtener(int $id): ?Instructor
    {
        return Instructor::with([
            'persona.tipoDocumento',
            'regional',
            'instructorFichas.ficha.programaFormacion'
        ])->find($id);
    }

    /**
     * Crea un nuevo instructor
     *
     * @param array $datos
     * @return Instructor
     * @throws \Exception
     */
    public function crear(array $datos, array $jornadasIds = []): Instructor
    {
        return DB::transaction(function () use ($datos, $jornadasIds) {
            // Validar que la persona existe y no sea ya instructor
            $persona = Persona::with(['instructor', 'user'])->findOrFail($datos['persona_id']);

            if ($persona->instructor) {
                throw new \Exception('Esta persona ya es instructor.');
            }

            if (!$persona->user) {
                throw new \Exception('Esta persona no tiene un usuario asociado.');
            }

            // Preparar datos para crear instructor
            $datosInstructor = [
                'persona_id' => $persona->id,
                'regional_id' => $datos['regional_id'],
                'anos_experiencia' => $datos['anos_experiencia'] ?? null,
                'experiencia_laboral' => $datos['experiencia_laboral'] ?? null,
                'status' => true,
                'user_create_id' => $datos['user_create_id'] ?? null,
            ];

            // Agregar campos nuevos si existen
            $camposNuevos = [
                'tipo_vinculacion_id',
                'centro_formacion_id',
                'experiencia_instructor_meses',
                'fecha_ingreso_sena',
                'nivel_academico_id',
                'titulos_obtenidos',
                'instituciones_educativas',
                'certificaciones_tecnicas',
                'cursos_complementarios',
                'formacion_pedagogia',
                'areas_experticia',
                'competencias_tic',
                'idiomas',
                'habilidades_pedagogicas',
                'documentos_adjuntos',
                'numero_contrato',
                'fecha_inicio_contrato',
                'fecha_fin_contrato',
                'supervisor_contrato',
                'eps',
                'arl'
            ];

            foreach ($camposNuevos as $campo) {
                if (isset($datos[$campo])) {
                    $datosInstructor[$campo] = $datos[$campo];
                }
            }

            // Crear instructor
            $instructor = Instructor::create($datosInstructor);

            // Asignar especialidades si se proporcionan
            if (!empty($datos['especialidades'])) {
                $this->asignarEspecialidades($instructor, $datos['especialidades']);
            }

            // Sincronizar jornadas (many-to-many)
            if (!empty($jornadasIds)) {
                $pivotData = [];
                foreach ($jornadasIds as $jornadaId) {
                    $pivotData[$jornadaId] = [
                        'user_create_id' => auth()->id(),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                }
                $instructor->jornadas()->sync($pivotData);
            }

            // Sincronizar solo el rol de instructor (evita duplicados)
            $persona->user->syncRoles(['INSTRUCTOR']);

            Log::info('Instructor creado exitosamente', [
                'instructor_id' => $instructor->id,
                'persona_id' => $persona->id,
                'user_id' => $persona->user->id,
            ]);

            return $instructor;
        });
    }

    /**
     * Actualiza un instructor
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        return DB::transaction(function () use ($id, $datos) {
            $instructor = Instructor::findOrFail($id);
            $persona = Persona::findOrFail($instructor->persona_id);

            // Actualizar persona
            if (isset($datos['persona'])) {
                $persona->update($datos['persona']);
            }

            // Actualizar instructor
            $instructor->update([
                'regional_id' => $datos['regional_id'] ?? $instructor->regional_id,
                'anos_experiencia' => $datos['anos_experiencia'] ?? $instructor->anos_experiencia,
                'experiencia_laboral' => $datos['experiencia_laboral'] ?? $instructor->experiencia_laboral,
            ]);

            // Actualizar usuario asociado
            if (isset($datos['persona']['email'])) {
                $user = User::where('persona_id', $persona->id)->first();
                if ($user) {
                    $user->update([
                        'email' => $datos['persona']['email'],
                        'password' => Hash::make($datos['persona']['numero_documento'] ?? $persona->numero_documento),
                    ]);
                }
            }

            Log::info('Instructor actualizado exitosamente', [
                'instructor_id' => $instructor->id,
            ]);

            return true;
        });
    }

    /**
     * Elimina un instructor
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $instructor = Instructor::findOrFail($id);
            $personaId = $instructor->persona_id;

            // Remover rol de instructor del usuario
            $user = User::where('persona_id', $personaId)->first();
            if ($user && $user->hasRole('INSTRUCTOR')) {
                $user->removeRole('INSTRUCTOR');
            }

            // Eliminar instructor
            $instructor->delete();

            Log::info('Instructor eliminado exitosamente', [
                'instructor_id' => $id,
            ]);

            return true;
        });
    }

    /**
     * Asigna especialidades a un instructor
     *
     * @param Instructor $instructor
     * @param array $especialidadesIds
     * @return void
     */
    protected function asignarEspecialidades(Instructor $instructor, array $especialidadesIds): void
    {
        if (empty($especialidadesIds)) {
            return;
        }

        // Validar que los IDs existan en la base de datos
        $especialidadesValidas = \App\Models\RedConocimiento::whereIn('id', $especialidadesIds)
            ->pluck('id')
            ->toArray();

        $especialidadesFormateadas = [
            'principal' => null,
            'secundarias' => []
        ];

        // La primera especialidad es la principal (guardar ID, no nombre)
        if (count($especialidadesValidas) > 0) {
            $especialidadesFormateadas['principal'] = $especialidadesValidas[0];

            // Las demás son secundarias (guardar IDs, no nombres)
            for ($i = 1; $i < count($especialidadesValidas); $i++) {
                $especialidadesFormateadas['secundarias'][] = $especialidadesValidas[$i];
            }
        }

        $instructor->especialidades = $especialidadesFormateadas;
        $instructor->save();
    }

    /**
     * Cambia el estado de un instructor
     *
     * @param int $id
     * @param bool $estado
     * @return bool
     */
    public function cambiarEstado(int $id, bool $estado): bool
    {
        $instructor = Instructor::findOrFail($id);
        $instructor->update(['status' => $estado]);

        Log::info('Estado de instructor cambiado', [
            'instructor_id' => $id,
            'nuevo_estado' => $estado,
        ]);

        return true;
    }

    /**
     * Obtiene estadísticas de instructores
     *
     * @return array
     */
    public function obtenerEstadisticas(): array
    {
        return [
            'total' => Instructor::count(),
            'activos' => Instructor::where('status', true)->count(),
            'inactivos' => Instructor::where('status', false)->count(),
            'con_fichas' => Instructor::whereHas('instructorFichas')->count(),
        ];
    }

    /**
     * Obtiene instructores para select/dropdown
     *
     * @param int|null $regionalId
     * @return Collection
     */
    public function obtenerParaSelect(?int $regionalId = null): Collection
    {
        $query = Instructor::with('persona')
            ->where('status', true);

        if ($regionalId) {
            $query->where('regional_id', $regionalId);
        }

        return $query->get()->map(function ($instructor) {
            return [
                'id' => $instructor->id,
                'nombre' => $instructor->persona->nombre_completo ?? 'Sin nombre',
                'regional_id' => $instructor->regional_id,
            ];
        });
    }
}

