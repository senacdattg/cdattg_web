<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoriaCaracterizacionComplementariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            'Grupos poblacionales' => [
                'AFROCOLOMBIANO',
                'AFROCOLOMBIANOS_DESPLAZADOS_POR_LA_VIOLENCIA',
                'AFROCOLOMBIANOS_DESPLAZADOS_POR_LA_VIOLENCIA_CABEZ',
                'INDÍGENA',
                'INDÍGENAS_DESPLAZADOS_POR_LA_VIOLENCIA',
                'INDÍGENAS_DESPLAZADOS_POR_LA_VIOLENCIA_CABEZA_DE_F',
                'GITANO_ROM',
                'PALENQUERO',
                'RAIZAL',
                'NEGRO',
            ],
            'Condición familiar y social' => [
                'MUJER_CABEZA_DE_FAMILIA',
                'DESPLAZADOS_POR_LA_VIOLENCIA_CABEZA_DE_FAMILIA',
                'DESPLAZADOS_POR_FENÓMENOS_NATURALES_CABEZA_DE_FAMI',
                'JÓVENES_VULNERABLES',
                'ADOLESCENTE_TRABAJADOR',
                'ADOLESCENTE_EN_CONFLICTO_CON_LA_LEY_PENAL',
                'PERSONAS_EN_PROCESO_DE_REINTEGRACIÓN',
                'EMPRENDEDORES',
                'MICROEMPRESAS',
                'ARTESANOS',
                'CAMPESINO',
                'SOLDADOS_CAMPESINOS',
            ],
            'Condición de discapacidad' => [
                'DISCAPACIDAD_INTELECTUAL',
                'DISCAPACIDAD_AUDITIVA',
                'DISCAPACIDAD_FÍSICA',
                'DISCAPACIDAD_VISUAL',
                'DISCAPACIDAD_PSICOSOCIAL',
                'DISCAPACIDAD_MÚLTIPLE',
                'SORDOCEGUERA',
                'DESPLAZADOS_DISCAPACITADOS',
            ],
            'Víctimas del conflicto armado' => [
                'ABANDONO_O_DESPOJO_FORZADO_DE_TIERRAS',
                'ACTOS_TERRORISTA/ATENTADOS/COMBATES/ENFRENTAMIENTOS/HOSTIGAMIENTOS',
                'ADOLESCENTE_DESVINCULADO_DE_GRUPOS_ARMADOS_ORGANIZ',
                'DELITOS_CONTRA_LA_LIBERTAD_Y_LA_INTEGRIDAD_SEXUAL_EN_DESARROLLO_DEL_CONFLICTO_ARMADO',
                'DESAPARICIÓN_FORZADA',
                'DESPLAZADOS_POR_LA_VIOLENCIA',
                'RECLUTAMIENTO_FORZADO',
                'SECUESTRO',
                'HOMICIDIO_/_MASACRE',
                'HERIDO',
                'SOBREVIVIENTES_MINAS_ANTIPERSONALES',
                'MINAS_ANTIPERSONAL,_MUNICIÓN_SIN_EXPLOTAR,Y_ARTEFACTO«EXPLOSIVO_IMPROVISADO»',
                'AMENAZA',
            ],
            'Desastres naturales y desplazamiento' => [
                'DESPLAZADOS_POR_FENÓMENOS_NATURALES',
            ],
            'Remitidos e institucionales' => [
                'INPEC',
                'REMITIDOS_POR_EL_CIE',
                'REMITIDOS_POR_EL_PAL',
            ],
            'Sin clasificación específica' => [
                'NINGUNA',
            ],
        ];

        foreach ($data as $groupName => $items) {
            $groupId = DB::table('categorias_caracterizacion_complementarios')->insertGetId([
                'nombre' => $groupName,
                'slug' => Str::slug($groupName),
                'activo' => 1,
                'parent_id' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            foreach ($items as $itemName) {
                DB::table('categorias_caracterizacion_complementarios')->insert([
                    'nombre' => $itemName,
                    'slug' => Str::slug($itemName),
                    'activo' => 1,
                    'parent_id' => $groupId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}