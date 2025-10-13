<?php

namespace App\Repositories;

use App\Models\Login;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class LoginRepository
{
    /**
     * Registra un intento de login
     *
     * @param array $datos
     * @return Login
     */
    public function registrar(array $datos): Login
    {
        return Login::create([
            'user_id' => $datos['user_id'] ?? null,
            'email' => $datos['email'],
            'ip_address' => $datos['ip_address'] ?? request()->ip(),
            'user_agent' => $datos['user_agent'] ?? request()->userAgent(),
            'exitoso' => $datos['exitoso'] ?? false,
            'fecha_hora' => now(),
        ]);
    }

    /**
     * Obtiene intentos recientes por usuario
     *
     * @param int $userId
     * @param int $limite
     * @return Collection
     */
    public function obtenerIntentosPorUsuario(int $userId, int $limite = 10): Collection
    {
        return Login::where('user_id', $userId)
            ->orderBy('fecha_hora', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obtiene intentos fallidos recientes por email
     *
     * @param string $email
     * @param int $minutos
     * @return int
     */
    public function contarIntentosFallidosRecientes(string $email, int $minutos = 15): int
    {
        return Login::where('email', $email)
            ->where('exitoso', false)
            ->where('fecha_hora', '>=', Carbon::now()->subMinutes($minutos))
            ->count();
    }

    /**
     * Obtiene estadísticas de login
     *
     * @param string $fechaInicio
     * @param string $fechaFin
     * @return array
     */
    public function obtenerEstadisticas(string $fechaInicio, string $fechaFin): array
    {
        $intentos = Login::whereBetween('fecha_hora', [$fechaInicio, $fechaFin])->get();

        return [
            'total_intentos' => $intentos->count(),
            'exitosos' => $intentos->where('exitoso', true)->count(),
            'fallidos' => $intentos->where('exitoso', false)->count(),
            'usuarios_unicos' => $intentos->pluck('user_id')->unique()->count(),
        ];
    }
}

