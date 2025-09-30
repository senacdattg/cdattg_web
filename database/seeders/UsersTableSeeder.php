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
        $superAdmin = User::create([
            'email' => 'superAdmin@superAdmin.com',
            'password' => Hash::make('123456'),
            'status' => 1,
            'persona_id' => 1,
        ]);

        $superAdmin->assignRole('SUPER ADMINISTRADOR');

        $admin = User::create([
            'email' => 'admin@admin.com',
            'password' => Hash::make('123456'),
            'status' => 1,
            'persona_id' => 2,
        ]);

        $admin->assignRole('ADMINISTRADOR');

        $instructor = User::create([
            'email' => 'instructor@instructor.com',
            'password' => Hash::make('123456'),
            'status' => 1,
            'persona_id' => 3,
        ]);

        $instructor->assignRole('INSTRUCTOR');

        // Crear usuarios de prueba con rol APRENDIZ
        // Verificar si existen personas para asignar como aprendices
        $personasDisponibles = Persona::whereDoesntHave('user')
            ->orWhereDoesntHave('aprendiz')
            ->take(3)
            ->get();

        if ($personasDisponibles->count() >= 3) {
            // Aprendiz 1
            $aprendiz1 = User::create([
                'email' => 'aprendiz1@sena.edu.co',
                'password' => Hash::make('123456'),
                'status' => 1,
                'persona_id' => $personasDisponibles[0]->id,
            ]);
            $aprendiz1->assignRole('APRENDIZ');

            // Crear registro en tabla aprendices
            Aprendiz::create([
                'persona_id' => $personasDisponibles[0]->id,
                'estado' => 1,
            ]);

            // Aprendiz 2
            $aprendiz2 = User::create([
                'email' => 'aprendiz2@sena.edu.co',
                'password' => Hash::make('123456'),
                'status' => 1,
                'persona_id' => $personasDisponibles[1]->id,
            ]);
            $aprendiz2->assignRole('APRENDIZ');

            Aprendiz::create([
                'persona_id' => $personasDisponibles[1]->id,
                'estado' => 1,
            ]);

            // Aprendiz 3
            $aprendiz3 = User::create([
                'email' => 'aprendiz3@sena.edu.co',
                'password' => Hash::make('123456'),
                'status' => 1,
                'persona_id' => $personasDisponibles[2]->id,
            ]);
            $aprendiz3->assignRole('APRENDIZ');

            Aprendiz::create([
                'persona_id' => $personasDisponibles[2]->id,
                'estado' => 1,
            ]);
        }
    }
}
