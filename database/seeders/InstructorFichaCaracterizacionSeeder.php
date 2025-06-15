<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\InstructorFichaCaracterizacion;

class InstructorFichaCaracterizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        InstructorFichaCaracterizacion::create([
            'id' => 1,
            'instructor_id' => 1,
            'ficha_id' => 1,
            'fecha_inicio' => '2024-04-15',
            'fecha_fin' => '2026-07-14',
            'total_horas_instructor' => 2400,
        ]);
    }
}
