<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ProgramaFormacion;

class ProgramasFormacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programas_formacion = [
            'ANÁLISIS Y DESARROLLO DE SOFTWARE',
        ];

        foreach ($programas_formacion as $programa) {
            ProgramaFormacion::create([
                'codigo' => '228118',
                'nombre' => $programa,
                'red_conocimiento_id' => 15,
                'nivel_formacion_id' => 2
            ]);
        }
    }
}
