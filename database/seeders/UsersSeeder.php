<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'info@dataguaviare.com.co',
                'password' => 'Guaviare25.',
                'persona_id' => 1,
                'role' => 'BOT',
            ],
            [
                'email' => 'superadmin@dataguaviare.com',
                'password' => 'Guaviare25.',
                'persona_id' => 2,
                'role' => 'SUPER ADMINISTRADOR',
            ],
            [
                'email' => 'admin@dataguaviare.com',
                'password' => 'Guaviare25.',
                'persona_id' => 2,
                'role' => 'ADMINISTRADOR',
            ],
            [
                'email' => 'instructor@dataguaviare.com',
                'password' => 'Guaviare25.',
                'persona_id' => 3,
                'role' => 'INSTRUCTOR',
            ],
            [
                'email' => 'aprendiz1@dataguaviare.com',
                'password' => 'Guaviare25!',
                'persona_id' => 5,
                'role' => 'APRENDIZ',
            ],
            [
                'email' => 'aprendiz2@dataguaviare.com',
                'password' => 'Guaviare25!',
                'persona_id' => 6,
                'role' => 'APRENDIZ',
            ],
        ];

        foreach ($users as $userData) {
            $this->createOrUpdateUser($userData);
        }
    }

    /**
     * Crea o actualiza un usuario y asigna su rol
     *
     * @param array<string, mixed> $userData
     */
    private function createOrUpdateUser(array $userData): void
    {
        $user = User::updateOrCreate(
            ['email' => $userData['email']],
            [
                'password' => Hash::make($userData['password']),
                'status' => 1,
                'persona_id' => $userData['persona_id'],
                'email_verified_at' => now(),
            ]
        );

        if (!$user->hasRole($userData['role'])) {
            $user->assignRole($userData['role']);
        }
    }
}
