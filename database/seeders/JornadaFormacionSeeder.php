<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\JornadaFormacion;

class JornadaFormacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jornadas = [
            [
                'jornada' => 'MAÑANA'
            ],
            [
                'jornada' => 'TARDE'
            ],
            [
                'jornada' => 'NOCHE'
            ],
            [
                'jornada' => 'FINES DE SEMANA'
            ]
        ];

        foreach ($jornadas as $jornada) {
            JornadaFormacion::create($jornada);
        }
    }
}
