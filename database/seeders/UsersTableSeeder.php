<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $demoUser = User::updateOrCreate(
            ['email' => 'demo@cdattg.local'],
            [
                'password' => Hash::make('123456'),
                'status' => 1,
                'persona_id' => 2,
                'email_verified_at' => now(),
            ]
        );

        if (! $demoUser->hasRole('ADMINISTRADOR')) {
            $demoUser->assignRole('ADMINISTRADOR');
        }
    }
}
