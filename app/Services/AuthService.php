<?php

namespace App\Services;

use App\Repositories\LoginRepository;
use App\Repositories\UserRepository;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuthService
{
    protected LoginRepository $loginRepo;
    protected UserRepository $userRepo;

    public function __construct(
        LoginRepository $loginRepo,
        UserRepository $userRepo
    ) {
        $this->loginRepo = $loginRepo;
        $this->userRepo = $userRepo;
    }

    /**
     * Intenta autenticar un usuario
     *
     * @param string $email
     * @param string $password
     * @return array
     */
    public function intentarLogin(string $email, string $password): array
    {
        $datosLogin = [
            'email' => $email,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];

        // Verificar intentos fallidos recientes
        $intentosFallidos = $this->loginRepo->contarIntentosFallidosRecientes($email);

        if ($intentosFallidos >= 5) {
            $this->loginRepo->registrar([...$datosLogin, 'exitoso' => false]);

            return [
                'success' => false,
                'message' => 'Demasiados intentos fallidos. Intente más tarde.',
                'bloqueado' => true,
            ];
        }

        // Intentar login
        if (Auth::attempt(['email' => $email, 'password' => $password])) {
            $user = Auth::user();

            $this->loginRepo->registrar([
                ...$datosLogin,
                'user_id' => $user->id,
                'exitoso' => true,
            ]);

            Log::info('Login exitoso', [
                'user_id' => $user->id,
                'email' => $email,
            ]);

            return [
                'success' => true,
                'user' => $user,
                'message' => 'Login exitoso',
            ];
        }

        // Login fallido
        $this->loginRepo->registrar([...$datosLogin, 'exitoso' => false]);

        Log::warning('Login fallido', [
            'email' => $email,
            'intentos_fallidos' => $intentosFallidos + 1,
        ]);

        return [
            'success' => false,
            'message' => 'Credenciales inválidas',
            'intentos_restantes' => max(0, 5 - ($intentosFallidos + 1)),
        ];
    }

    /**
     * Registra logout
     *
     * @return void
     */
    public function logout(): void
    {
        $userId = Auth::id();

        Auth::logout();

        Log::info('Logout exitoso', [
            'user_id' => $userId,
        ]);
    }

    /**
     * Cambia contraseña de usuario
     *
     * @param int $userId
     * @param string $passwordActual
     * @param string $passwordNueva
     * @return bool
     * @throws \Exception
     */
    public function cambiarPassword(int $userId, string $passwordActual, string $passwordNueva): bool
    {
        $user = User::find($userId);

        if (!$user) {
            throw new \Exception('Usuario no encontrado.');
        }

        if (!Hash::check($passwordActual, $user->password)) {
            throw new \Exception('La contraseña actual es incorrecta.');
        }

        $actualizado = $this->userRepo->actualizar($userId, [
            'password' => Hash::make($passwordNueva),
        ]);

        if ($actualizado) {
            Log::info('Contraseña cambiada', [
                'user_id' => $userId,
            ]);
        }

        return $actualizado;
    }

    /**
     * Genera token de API para usuario
     *
     * @param User $user
     * @param string $nombre
     * @return string
     */
    public function generarTokenApi(User $user, string $nombre = 'API Token'): string
    {
        $token = $user->createToken($nombre)->plainTextToken;

        Log::info('Token API generado', [
            'user_id' => $user->id,
            'token_nombre' => $nombre,
        ]);

        return $token;
    }
}

