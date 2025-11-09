<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GuiasAprendizaje;
use Database\Seeders\Concerns\TruncatesTables;

class GuiasAprendizajeSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(GuiasAprendizaje::class);

        $guias_aprendizajes = [
            [
                'id' => 1,
                'codigo' => 'ADSO-16',
                'nombre' => 'Implantar_software',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 2,
                'codigo' => 'ADSO-17',
                'nombre' => ' Calidad_Software',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 3,
                'codigo' => 'C-900',
                'nombre' => 'TIC',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 4,
                'codigo' => 'ADSO-01',
                'nombre' => 'REQUISITOS_SOFTWARE',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 5,
                'codigo' => 'ADSO-02',
                'nombre' => 'PLANEAR',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 6,
                'codigo' => 'ADSO-03',
                'nombre' => 'MODELAR',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 7,
                'codigo' => 'ADSO-04',
                'nombre' => 'PROCESOS_LOGICOS',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 8,
                'codigo' => 'ADSO-05',
                'nombre' => 'VERIFICAR_MODELO',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 9,
                'codigo' => 'ADSO-06',
                'nombre' => 'ESTRUCTURAR_PROPUESTA',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 10,
                'codigo' => 'ADSO-07',
                'nombre' => 'ELABORAR_ARTEFACTOS',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 11,
                'codigo' => 'ADSO-08',
                'nombre' => 'ESTRUCTURAR_MODELO',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 12,
                'codigo' => 'ADSO-09',
                'nombre' => 'INTERFAZ_GRAFICA',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 13,
                'codigo' => 'ADSO-10',
                'nombre' => 'VERIFICAR_VARIABLES',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 14,
                'codigo' => 'ADSO-11',
                'nombre' => 'AISLAMIENTO',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 15,
                'codigo' => 'ADSO-12',
                'nombre' => 'CONSTRUIR_BASE_DATOS',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 16,
                'codigo' => 'ADSO-13',
                'nombre' => 'CREAR_FRONT_END',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 17,
                'codigo' => 'ADSO-14',
                'nombre' => 'CODIFICAR',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 18,
                'codigo' => 'ADSO-15',
                'nombre' => 'PRUEBA_SOFTWARE',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
        ];

        foreach ($guias_aprendizajes as $guia_aprendizaje) {
            GuiasAprendizaje::create($guia_aprendizaje);
        }
    }
}
