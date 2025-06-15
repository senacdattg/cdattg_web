<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FichaCaracterizacion;
use Carbon\Carbon;

class FichaCaracterizacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        FichaCaracterizacion::create([
            'id' => 1,
            'programa_formacion_id' => 1,
            'ficha' => '2923560',
            'instructor_id' => 1,
            'fecha_inicio' => Carbon::parse('2024-04-15'),
            'fecha_fin' => Carbon::parse('2026-07-14'),
            'ambiente_id' => 60,
            'modalidad_formacion_id' => 18,
            'sede_id' => 2,
            'jornada_id' => 1,
            'total_horas' => 3345,
            'user_create_id' => 1,
            'user_edit_id' => 1,
            'status' => true
        ]);
    }
}
