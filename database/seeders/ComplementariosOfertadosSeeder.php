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
                'nombre' => 'Curso de Inglés Básico',
                'descripcion' => 'Curso introductorio al idioma inglés.',
                'duracion' => 40,
                'cupos' => 20,
                'estado' => 0,
                'modalidad_id' => 18, // PRESENCIAL
                'jornada_id' => 1, // MAÑANA
            ],
            [
                'codigo' => 'COMP002',
                'nombre' => 'Taller de Programación Web',
                'descripcion' => 'Aprende desarrollo web con HTML, CSS y JS.',
                'duracion' => 60,
                'cupos' => 15,
                'estado' => 0,
                'modalidad_id' => 19, // VIRTUAL
                'jornada_id' => 2, // TARDE
            ],
            [
                'codigo' => 'COMP003',
                'nombre' => 'Seminario de Liderazgo',
                'descripcion' => 'Desarrollo de habilidades de liderazgo.',
                'duracion' => 20,
                'cupos' => 10,
                'estado' => 1, // sin oferta
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
