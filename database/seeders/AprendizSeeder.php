<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Persona;
use App\Models\Aprendiz;

class AprendizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $personas = Persona::where('status', 1)->where('id', '>=', 4)->get();

        foreach ($personas as $persona) {
            Aprendiz::create([
                'persona_id' => $persona->id,
            ]);
        }
    }
}
