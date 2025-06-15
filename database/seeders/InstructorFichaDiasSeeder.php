<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InstructorFichaDias;

class InstructorFichaDiasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $instructor_ficha_dias = [
            [
                'instructor_ficha_id' => 1,
                'dia_id' => 12,
                'hora_inicio' => '06:30:00',
                'hora_fin' => '13:00:00',
            ],
            [
                'instructor_ficha_id' => 1,
                'dia_id' => 13,
                'hora_inicio' => '06:30:00',
                'hora_fin' => '13:00:00',
            ],
            [
                'instructor_ficha_id' => 1,
                'dia_id' => 16,
                'hora_inicio' => '06:30:00',
                'hora_fin' => '13:00:00',
            ],
            [
                'instructor_ficha_id' => 1,
                'dia_id' => 17,
                'hora_inicio' => '06:30:00',
                'hora_fin' => '13:00:00',
            ]
        ];

        foreach ($instructor_ficha_dias as $instructor_ficha_dia) {
            InstructorFichaDias::create($instructor_ficha_dia);
        }
    }
}
