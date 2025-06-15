<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FichaDiasFormacion;

class FichaDiasFormacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fichas_dias_formacion = [
            [
                'id' => 1,
                'ficha_id' => 1,
                'dia_id' => 1,
                'hora_inicio' => '06:30:00',
                'hora_fin' => '13:00:00'
            ],
            [
                'id' => 2,
                'ficha_id' => 1,
                'dia_id' => 2,
                'hora_inicio' => '06:30:00',
                'hora_fin' => '13:00:00'
            ],
            [
                'id' => 3,
                'ficha_id' => 1,
                'dia_id' => 3,
                'hora_inicio' => '06:30:00',
                'hora_fin' => '13:00:00'
            ],
            [
                'id' => 4,
                'ficha_id' => 1,
                'dia_id' => 4,
                'hora_inicio' => '06:30:00',
                'hora_fin' => '13:00:00'
            ],
            [
                'id' => 5,
                'ficha_id' => 1,
                'dia_id' => 5,
                'hora_inicio' => '06:30:00',
                'hora_fin' => '13:00:00'
            ],
            [
                'id' => 6,
                'ficha_id' => 1,
                'dia_id' => 6,
                'hora_inicio' => '07:00:00',
                'hora_fin' => '18:00:00'
            ]
        ];

        foreach ($fichas_dias_formacion as $ficha) {
            FichaDiasFormacion::create($ficha);
        }
    }
}
