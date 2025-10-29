<?php

namespace App\Repositories;

use App\Core\Traits\HasCache;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    use HasCache;


    public function __construct()
    {
        $this->cacheType = 'parametros';
        $this->cacheTags = ['usuarios', 'auth'];
    }    /**
     * Encuentra usuario por email
     *
     * @param string $email
     * @return User|null
     */
    public function encontrarPorEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Encuentra usuario por persona
     *
     * @param int $personaId
     * @return User|null
     */
    public function encontrarPorPersona(int $personaId): ?User
    {
        return User::where('persona_id', $personaId)
            ->with('persona')
            ->first();
    }

    /**
     * Obtiene usuarios por rol
     *
     * @param string $rol
     * @return Collection
     */
    public function obtenerPorRol(string $rol): Collection
    {
        return $this->cache("rol.{$rol}.usuarios", function () use ($rol) {
            return User::role($rol)
                ->with('persona')
                ->get();
        }, 60); // 1 hora
    }

    /**
     * Crea un nuevo usuario
     *
     * @param array $datos
     * @return User
     */
    public function crear(array $datos): User
    {
        $user = User::create($datos);
        $this->invalidarCache();
        return $user;
    }

    /**
     * Actualiza usuario
     *
     * @param int $id
     * @param array $datos
     * @return bool
     */
    public function actualizar(int $id, array $datos): bool
    {
        $actualizado = User::where('id', $id)->update($datos);
        $this->invalidarCache();
        return $actualizado;
    }

    /**
     * Invalida caché
     *
     * @return void
     */
    public function invalidarCache(): void
    {
        $this->flushCache();
    }
}

