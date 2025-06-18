<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Competencia;

class CompetenciaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $competencias = [
            [
                'codigo' => '38356',
                'nombre' => 'IMPLANTACIÓN DEL SOFTWARE',
                'descripcion' => 'Implementar la solución de software de acuerdo con los requisitos de operación y modelos de referencia',
                'duracion' => 144,
                'fecha_inicio' => '2026-01-01',
                'fecha_fin' => '2026-12-31',
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38368',
                'nombre' => 'CONSTRUCCIÓN DEL SOFTWARE',
                'descripcion' => 'Desarrollar la solución de software de acuerdo con el diseño y metodologías de desarrollo',
                'fecha_inicio' => '2025-04-21',
                'fecha_fin' => '2025-12-13',
                'duracion' => 1008,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38369',
                'nombre' => 'ADOPCIÓN DE BUENAS PRÁCTICAS EN EL PROCESO DE DESARROLLO DE SOFTWARE',
                'descripcion' => 'Controlar la calidad del servicio de software de acuerdo con los estándares técnicos',
                'duracion' => 144,
                'fecha_inicio' => '2026-01-01',
                'fecha_fin' => '2026-12-31',
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '37371',
                'nombre' => 'TIC',
                'descripcion' => 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información',
                'fecha_inicio' => '2024-04-24',
                'fecha_fin' => '2024-05-10',
                'duracion' => 48,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38392',
                'nombre' => 'ESPECIFICACIÓN DE REQUISITOS DEL SOFTWARE',
                'descripcion' => 'Establecer requisitos de la solución de software de acuerdo con estándares y procedimiento técnico',
                'fecha_inicio' => '2024-05-11',
                'fecha_fin' => '2025-06-25',
                'duracion' => 384,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38376',
                'nombre' => 'ANÁLISIS DE LA ESPECIFICACIÓN DE REQUISITOS DEL SOFTWARE',
                'descripcion' => 'Evaluar requisitos de la solución de software de acuerdo con metodologías de análisis y estándares',
                'fecha_inicio' => '2024-06-28',
                'fecha_fin' => '2024-09-21',
                'duracion' => 288,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38367',
                'nombre' => 'ELABORACIÓN DE LA PROPUESTA TÉCNICA DEL SOFTWARE',
                'descripcion' => 'Estructurar propuesta técnica de servicio de tecnología de la información según requisitos técnicos y normativa',
                'fecha_inicio' => '2024-09-23',
                'fecha_fin' => '2024-11-08',
                'duracion' => 48,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38362',
                'nombre' => 'MODELADO DE LOS ARTEFACTOS DEL SOFTWARE',
                'descripcion' => 'Diseñar la solución de software de acuerdo con procedimientos y requisitos técnicos',
                'fecha_inicio' => '2024-11-09',
                'fecha_fin' => '2025-03-18',
                'duracion' => 336,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
        ];

        

        foreach ($competencias as $competencia) {
            Competencia::create($competencia);
        }
    }
}
