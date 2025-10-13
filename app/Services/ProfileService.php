<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class ProfileService
{
    public function actualizarPerfil(User $user, array $datos): bool
    {
        try {
            $user->name = $datos['name'];
            $user->email = $datos['email'];

            if (isset($datos['password']) && !empty($datos['password'])) {
                if (!Hash::check($datos['current_password'], $user->password)) {
                    throw new \Exception('La contraseña actual no es correcta');
                }
                $user->password = Hash::make($datos['password']);
            }

            $user->save();

            Log::info('Perfil actualizado', ['user_id' => $user->id]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al actualizar perfil: ' . $e->getMessage());
            throw $e;
        }
    }

    public function cambiarContrasena(User $user, string $currentPassword, string $newPassword): bool
    {
        try {
            if (!Hash::check($currentPassword, $user->password)) {
                throw new \Exception('La contraseña actual no es correcta');
            }

            $user->password = Hash::make($newPassword);
            $user->save();

            Log::info('Contraseña cambiada', ['user_id' => $user->id]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error al cambiar contraseña: ' . $e->getMessage());
            throw $e;
        }
    }
}

