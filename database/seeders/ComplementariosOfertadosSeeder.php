<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComplementariosOfertadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $complementarios = [
            [
                'codigo' => 'COMP001',
                'nombre' => 'Auxiliar de Cocina',
                'descripcion' => 'Fundamentos de cocina, manipulación de alimentos y técnicas básicas de preparación.',
                'duracion' => 40,
                'cupos' => 20,
                'estado' => 1, // Offered
                'modalidad_id' => 18, // PRESENCIAL
                'jornada_id' => 1, // MAÑANA
            ],
            [
                'codigo' => 'COMP002',
                'nombre' => 'Acabados en Madera',
                'descripcion' => 'Técnicas de acabado, barnizado y restauración de muebles de madera.',
                'duracion' => 60,
                'cupos' => 15,
                'estado' => 1, // Offered
                'modalidad_id' => 19, // VIRTUAL
                'jornada_id' => 2, // TARDE
            ],
            [
                'codigo' => 'COMP003',
                'nombre' => 'Confección de Prendas',
                'descripcion' => 'Técnicas básicas de corte, confección y terminado de prendas de vestir.',
                'duracion' => 50,
                'cupos' => 10,
                'estado' => 0, // Not offered
                'modalidad_id' => 20, // MIXTA
                'jornada_id' => 3, // NOCHE
            ],
        ];

        foreach ($complementarios as $complementario) {
            $id = DB::table('complementarios_ofertados')->insertGetId($complementario);

            // Add sample dias (e.g., LUNES, MIERCOLES, VIERNES for first, etc.)
            $dias = [
                ['dia_id' => 12, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], // LUNES
                ['dia_id' => 14, 'hora_inicio' => '08:00', 'hora_fin' => '12:00'], // MIERCOLES
            ];
            foreach ($dias as $dia) {
                DB::table('complementarios_ofertados_dias_formacion')->insert([
                    'complementario_id' => $id,
                    'dia_id' => $dia['dia_id'],
                    'hora_inicio' => $dia['hora_inicio'],
                    'hora_fin' => $dia['hora_fin'],
                ]);
            }
        }
    }
}
