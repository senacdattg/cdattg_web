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
    }
}
