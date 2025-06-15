<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DiasFormacion;

class DiasFormacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dias_formacion = [
            'LUNES',
            'MARTES',
            'MIÉRCOLES',
            'JUEVES',
            'VIERNES',
            'SABADO'
        ];

        foreach ($dias_formacion as $dia) {
            DiasFormacion::create([
                'nombre' => $dia
            ]);
        }
    }
}
