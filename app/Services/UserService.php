<?php

namespace App\Services;

use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserService
{
    protected UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Cambia el estado de un usuario
     *
     * @param int $userId
     * @param int $usuarioActualId
     * @return bool
     * @throws \Exception
     */
    public function cambiarEstado(int $userId, int $usuarioActualId): bool
    {
        // Evitar que un usuario cambie su propio estado
        if ($userId === $usuarioActualId) {
            throw new \Exception('No puedes modificar tu propio estado.');
        }

        return DB::transaction(function () use ($userId) {
            $user = User::find($userId);

            if (!$user) {
                throw new \Exception('Usuario no encontrado.');
            }

            $nuevoEstado = !$user->status;

            $actualizado = $this->repository->actualizar($userId, [
                'status' => $nuevoEstado,
            ]);

            Log::info('Estado de usuario cambiado', [
                'user_id' => $userId,
                'nuevo_estado' => $nuevoEstado,
            ]);

            return $actualizado;
        });
    }

    /**
     * Asigna roles a un usuario
     *
     * @param int $userId
     * @param array $roles
     * @return bool
     */
    public function asignarRoles(int $userId, array $roles): bool
    {
        return DB::transaction(function () use ($userId, $roles) {
            $user = User::findOrFail($userId);

            $user->syncRoles($roles);

            Log::info('Roles asignados', [
                'user_id' => $userId,
                'roles' => $roles,
            ]);

            return true;
        });
    }

    public function agregarRoles(int $userId, array $roles): bool
    {
        if (empty($roles)) {
            return true;
        }

        return DB::transaction(function () use ($userId, $roles) {
            $user = User::findOrFail($userId);

            $rolesValidos = collect($roles)
                ->map(static fn(string $rol) => strtoupper($rol))
                ->unique()
                ->values();

            $rolesDisponibles = \Spatie\Permission\Models\Role::whereIn('name', $rolesValidos)->pluck('name');

            if ($rolesDisponibles->isEmpty()) {
                return true;
            }

            $rolesPrevios = $user->roles->pluck('name');

            $rolesDisponibles->each(static function (string $rol) use ($user): void {
                if (!$user->hasRole($rol)) {
                    $user->assignRole($rol);
                }
            });

            Log::info('Roles agregados al usuario', [
                'user_id' => $userId,
                'roles_previos' => $rolesPrevios->all(),
                'roles_nuevos' => $rolesDisponibles->all(),
                'roles_finales' => $user->roles->pluck('name')->all(),
            ]);

            return true;
        });
    }

    /**
     * Obtiene usuarios por rol
     *
     * @param string $rol
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function obtenerPorRol(string $rol)
    {
        return $this->repository->obtenerPorRol($rol);
    }
}

