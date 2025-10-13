<?php

namespace App\Services;

use App\Repositories\PersonaRepository;
use App\Repositories\UserRepository;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

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
        return DB::transaction(function () use ($datos) {
            $datos['user_create_id'] = auth()->id();
            $datos['user_edit_id'] = auth()->id();

            $persona = Persona::create($datos);

            // Crear usuario asociado
            $this->crearUsuarioPersona($persona);

            Log::info('Persona creada', [
                'persona_id' => $persona->id,
                'documento' => $persona->numero_documento,
            ]);

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
        return DB::transaction(function () use ($id, $datos) {
            $datos['user_edit_id'] = auth()->id();
            
            $actualizado = Persona::where('id', $id)->update($datos);

            if ($actualizado) {
                Log::info('Persona actualizada', [
                    'persona_id' => $id,
                ]);
            }

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

            if ($eliminado) {
                Log::info('Persona eliminada', [
                    'persona_id' => $id,
                ]);
            }

            return $eliminado;
        });
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
}

