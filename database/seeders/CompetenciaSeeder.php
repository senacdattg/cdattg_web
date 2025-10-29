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
                'status' => true,
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
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '37371',
                'nombre' => 'TIC',
                'descripcion' => 'Utilizar herramientas informáticas de acuerdo con las necesidades de manejo de información',
                'duracion' => 48,
                'fecha_inicio' => '2024-04-24',
                'fecha_fin' => '2024-05-10',
                'status' => false,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38392',
                'nombre' => 'ESPECIFICACIÓN DE REQUISITOS DEL SOFTWARE',
                'descripcion' => 'Establecer requisitos de la solución de software de acuerdo con estándares y procedimiento técnico',
                'duracion' => 384,
                'fecha_inicio' => '2024-05-11',
                'fecha_fin' => '2025-06-25',
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38376',
                'nombre' => 'ANÁLISIS DE LA ESPECIFICACIÓN DE REQUISITOS DEL SOFTWARE',
                'descripcion' => 'Evaluar requisitos de la solución de software de acuerdo con metodologías de análisis y estándares',
                'duracion' => 288,
                'fecha_inicio' => '2024-06-28',
                'fecha_fin' => '2024-09-21',
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38367',
                'nombre' => 'ELABORACIÓN DE LA PROPUESTA TÉCNICA DEL SOFTWARE',
                'descripcion' => 'Estructurar propuesta técnica de servicio de tecnología de la información según requisitos técnicos y normativa',
                'duracion' => 48,
                'fecha_inicio' => '2024-09-23',
                'fecha_fin' => '2024-11-08',
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38362',
                'nombre' => 'MODELADO DE LOS ARTEFACTOS DEL SOFTWARE',
                'descripcion' => 'Diseñar la solución de software de acuerdo con procedimientos y requisitos técnicos',
                'duracion' => 336,
                'fecha_inicio' => '2024-11-09',
                'fecha_fin' => '2025-03-18',
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38368',
                'nombre' => 'CONSTRUCCIÓN DEL SOFTWARE',
                'descripcion' => 'Desarrollar la solución de software de acuerdo con el diseño y metodologías de desarrollo',
                'duracion' => 1008,
                'fecha_inicio' => '2025-04-21',
                'fecha_fin' => '2025-12-13',
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38370',
                'nombre' => 'PRUEBAS Y VERIFICACIÓN DEL SOFTWARE',
                'descripcion' => 'Realizar pruebas de calidad y verificación del software según estándares establecidos',
                'duracion' => 96,
                'fecha_inicio' => '2025-01-15',
                'fecha_fin' => '2025-04-15',
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38380',
                'nombre' => 'MANTENIMIENTO Y SOPORTE DE SOFTWARE',
                'descripcion' => 'Proporcionar mantenimiento correctivo y evolutivo del software según necesidades del cliente',
                'duracion' => 200,
                'fecha_inicio' => '2025-06-01',
                'fecha_fin' => '2025-12-31',
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38390',
                'nombre' => 'GESTIÓN DE PROYECTOS DE SOFTWARE',
                'descripcion' => 'Administrar proyectos de desarrollo de software aplicando metodologías ágiles y tradicionales',
                'duracion' => 120,
                'fecha_inicio' => '2024-03-01',
                'fecha_fin' => '2024-06-30',
                'status' => false,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'codigo' => '38400',
                'nombre' => 'SEGURIDAD INFORMÁTICA',
                'descripcion' => 'Implementar medidas de seguridad informática según estándares y normativas vigentes',
                'duracion' => 72,
                'fecha_inicio' => '2025-09-01',
                'fecha_fin' => '2025-11-30',
                'status' => true,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
        ];

        foreach ($competencias as $competencia) {
            Competencia::create($competencia);
        }

        $this->command->info('✓ Competencias creadas exitosamente con variedad de duraciones y estados.');
    }
}
