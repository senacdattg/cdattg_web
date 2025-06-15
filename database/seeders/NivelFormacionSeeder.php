<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\NivelFormacion;

class NivelFormacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $niveles_formacion = [
            'TÉCNICO',
            'TECNOLOGO',
            'ESPECIALIZACIÓN',
            'MAESTRÍA',
            'DOCTORADO',
        ];

        foreach ($niveles_formacion as $nivel) {
            NivelFormacion::create([
                'nivel_formacion' => $nivel
            ]);
        }
    }
}
