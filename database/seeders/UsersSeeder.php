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
        $bot = User::updateOrCreate(
            ['email' => 'bot@dataguaviare.com'],
            [
                'password' => Hash::make('Guaviare25.'),
                'status' => 1,
                'persona_id' => 1,
                'email_verified_at' => now(),
            ]
        );

        if (! $bot->hasRole('BOT')) {
            $bot->assignRole('BOT');
        }

        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@dataguaviare.com'],
            [
                'password' => Hash::make('Guaviare25.'),
                'status' => 1,
                'persona_id' => 2,
                'email_verified_at' => now(),
            ]
        );

        if (! $superAdmin->hasRole('SUPER ADMINISTRADOR')) {
            $superAdmin->assignRole('SUPER ADMINISTRADOR');
        }

        $admin = User::updateOrCreate(
            ['email' => 'admin@dataguaviare.com'],
            [
                'password' => Hash::make('Guaviare25.'),
                'status' => 1,
                'persona_id' => 2,
                'email_verified_at' => now(),
            ]
        );

        if (! $admin->hasRole('ADMINISTRADOR')) {
            $admin->assignRole('ADMINISTRADOR');
        }

        $instructor = User::updateOrCreate(
            ['email' => 'instructor@dataguaviare.com'],
            [
                'password' => Hash::make('Guaviare25.'),
                'status' => 1,
                'persona_id' => 3,
                'email_verified_at' => now(),
            ]
        );

        if (! $instructor->hasRole('INSTRUCTOR')) {
            $instructor->assignRole('INSTRUCTOR');
        }

        $aprendiz1 = User::updateOrCreate(
            ['email' => 'aprendiz1@dataguaviare.com'],
            [
                'password' => Hash::make('Guaviare25!'),
                'status' => 1,
                'persona_id' => 5,
                'email_verified_at' => now(),
            ]
        );

        if (! $aprendiz1->hasRole('APRENDIZ')) {
            $aprendiz1->assignRole('APRENDIZ');
        }

        $aprendiz2 = User::updateOrCreate(
            ['email' => 'aprendiz2@dataguaviare.com'],
            [
                'password' => Hash::make('Guaviare25!'),
                'status' => 1,
                'persona_id' => 6,
                'email_verified_at' => now(),
            ]
        );

        if (! $aprendiz2->hasRole('APRENDIZ')) {
            $aprendiz2->assignRole('APRENDIZ');
        }
    }
}
