<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdatePersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Persona::whereKey(1)->update([
            'tipo_documento' => 3,
            'genero' => 9,
        ]);

        Persona::whereKey(2)->update([
            'tipo_documento' => 3,
            'genero' => 10,
        ]);
    }
}
