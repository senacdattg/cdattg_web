<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\User;
use App\Models\Aprendiz;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o actualizar Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superAdmin@superAdmin.com'],
            [
                'password' => Hash::make('123456'),
                'status' => 1,
                'persona_id' => 1,
            ]
        );
        if (!$superAdmin->hasRole('SUPER ADMINISTRADOR')) {
            $superAdmin->assignRole('SUPER ADMINISTRADOR');
        }

        // Crear o actualizar Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'password' => Hash::make('123456'),
                'status' => 1,
                'persona_id' => 2,
            ]
        );
        if (!$admin->hasRole('ADMINISTRADOR')) {
            $admin->assignRole('ADMINISTRADOR');
        }

        // Crear o actualizar Instructor
        $instructor = User::firstOrCreate(
            ['email' => 'instructor@instructor.com'],
            [
                'password' => Hash::make('123456'),
                'status' => 1,
                'persona_id' => 3,
            ]
        );
        if (!$instructor->hasRole('INSTRUCTOR')) {
            $instructor->assignRole('INSTRUCTOR');
        }

        // Crear usuarios de prueba con rol APRENDIZ
        // Verificar si existen personas para asignar como aprendices
        $personasDisponibles = Persona::whereDoesntHave('user')
            ->orWhereDoesntHave('aprendiz')
            ->take(3)
            ->get();

        if ($personasDisponibles->count() >= 3) {
            // Aprendiz 1
            $aprendiz1 = User::firstOrCreate(
                ['email' => 'aprendiz1@sena.edu.co'],
                [
                    'password' => Hash::make('123456'),
                    'status' => 1,
                    'persona_id' => $personasDisponibles[0]->id,
                ]
            );
            if (!$aprendiz1->hasRole('APRENDIZ')) {
                $aprendiz1->assignRole('APRENDIZ');
            }

            // Crear registro en tabla aprendices si no existe
            Aprendiz::firstOrCreate(
                ['persona_id' => $personasDisponibles[0]->id],
                ['estado' => 1]
            );

            // Aprendiz 2
            $aprendiz2 = User::firstOrCreate(
                ['email' => 'aprendiz2@sena.edu.co'],
                [
                    'password' => Hash::make('123456'),
                    'status' => 1,
                    'persona_id' => $personasDisponibles[1]->id,
                ]
            );
            if (!$aprendiz2->hasRole('APRENDIZ')) {
                $aprendiz2->assignRole('APRENDIZ');
            }

            Aprendiz::firstOrCreate(
                ['persona_id' => $personasDisponibles[1]->id],
                ['estado' => 1]
            );

            // Aprendiz 3
            $aprendiz3 = User::firstOrCreate(
                ['email' => 'aprendiz3@sena.edu.co'],
                [
                    'password' => Hash::make('123456'),
                    'status' => 1,
                    'persona_id' => $personasDisponibles[2]->id,
                ]
            );
            if (!$aprendiz3->hasRole('APRENDIZ')) {
                $aprendiz3->assignRole('APRENDIZ');
            }

            Aprendiz::firstOrCreate(
                ['persona_id' => $personasDisponibles[2]->id],
                ['estado' => 1]
            );
        }
    }
}
