<?php

namespace App\Services;

use App\Repositories\PersonaRepository;
use App\Repositories\UserRepository;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PersonaService
{
    protected PersonaRepository $repository;
    protected UserRepository $userRepo;

    public function __construct(
        PersonaRepository $repository,
        UserRepository $userRepo
    ) {
        $this->repository = $repository;
        $this->userRepo = $userRepo;
    }

    /**
     * Lista personas paginadas
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function listar(int $perPage = 10): LengthAwarePaginator
    {
        return Persona::with(['tipoDocumento', 'tipoGenero', 'user'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    /**
     * Obtiene persona con relaciones
     *
     * @param int $id
     * @return Persona|null
     */
    public function obtener(int $id): ?Persona
    {
        return Persona::with(['tipoDocumento', 'tipoGenero', 'user', 'aprendiz', 'instructor'])
            ->find($id);
    }

    /**
     * Crea una persona y su usuario
     *
     * @param array $datos
     * @return Persona
     */
    public function crear(array $datos): Persona
    {
        $userId = $this->obtenerIdUsuarioAutenticado();

        return DB::transaction(function () use ($datos, $userId) {
            $datos['user_create_id'] = $userId;
            $datos['user_edit_id'] = $userId;

            $caracterizacionesIds = collect($datos['caracterizacion_ids'] ?? [])
                ->filter()
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $datos['caracterizacion_id'] = $caracterizacionesIds->first() ?: null;
            unset($datos['caracterizacion_ids']);

            $persona = Persona::create($datos);

            if ($caracterizacionesIds->isNotEmpty()) {
                $persona->caracterizacionesComplementarias()->sync($caracterizacionesIds);
            }

            // Crear usuario asociado
            $this->crearUsuarioPersona($persona);

            return $persona;
        });
    }

    /**
     * Actualiza una persona
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        $userId = $this->obtenerIdUsuarioAutenticado();

        return DB::transaction(function () use ($id, $datos, $userId) {

            $datos['user_edit_id'] = $userId;
            $actualizado = Persona::where('id', $id)->update($datos);

            return $actualizado;
        });
    }

    /**
     * Elimina una persona
     *
     * @param int $id
     * @return bool
     * @throws \Exception
     */
    public function eliminar(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $persona = Persona::find($id);

            if (!$persona) {
                throw new \Exception('Persona no encontrada');
            }

            // Verificar si tiene dependencias
            if ($persona->aprendiz || $persona->instructor) {
                throw new \Exception('No se puede eliminar una persona que es aprendiz o instructor');
            }

            $eliminado = $persona->delete();

            return $eliminado;
        });
    }

    /**
     * Cambia estado del usuario de una persona
     *
     * @param int $personaId
     * @return bool
     */
    public function cambiarEstadoUsuario(int $personaId): bool
    {
        $persona = $this->obtener($personaId);

        if (!$persona || !$persona->user) {
            throw new \Exception('Persona o usuario no encontrado');
        }

        $nuevoEstado = !$persona->user->status;

        return $this->userRepo->actualizar($persona->user->id, [
            'status' => $nuevoEstado,
        ]);
    }

    /**
     * Crea usuario asociado a persona
     *
     * @param Persona $persona
     * @return User
     */
    protected function crearUsuarioPersona(Persona $persona): User
    {
        return $this->userRepo->crear([
            'email' => $persona->email,
            'password' => Hash::make($persona->numero_documento),
            'persona_id' => $persona->id,
        ]);
    }

    protected function obtenerIdUsuarioAutenticado(): int
    {
        $userId = Auth::id();

        if ($userId === null) {
            throw new AuthenticationException('Debe iniciar sesión para realizar esta acción.');
        }

        return $userId;
    }
}
