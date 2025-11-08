<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GuiasResultados;
use Database\Seeders\Concerns\TruncatesTables;

class ResultadosGuiasSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(GuiasResultados::class);

        $resultados_guias = [
            [
                'id' => 1,
                'guia_aprendizaje_id' => '1',
                'rap_id' => '1',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 2,
                'guia_aprendizaje_id' => '1',
                'rap_id' => '2',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 3,
                'guia_aprendizaje_id' => '1',
                'rap_id' => '2',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 4,
                'guia_aprendizaje_id' => '1',
                'rap_id' => '4',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 5,
                'guia_aprendizaje_id' => '2',
                'rap_id' => '5',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 6,
                'guia_aprendizaje_id' => '2',
                'rap_id' => '6',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 7,
                'guia_aprendizaje_id' => '2',
                'rap_id' => '7',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 8,
                'guia_aprendizaje_id' => '3',
                'rap_id' => '8',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 9,
                'guia_aprendizaje_id' => '3',
                'rap_id' => '9',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 10,
                'guia_aprendizaje_id' => '3',
                'rap_id' => '10',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 11,
                'guia_aprendizaje_id' => '3',
                'rap_id' => '11',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 12,
                'guia_aprendizaje_id' => '4',
                'rap_id' => '12',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 13,
                'guia_aprendizaje_id' => '4',
                'rap_id' => '13',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 14,
                'guia_aprendizaje_id' => '4',
                'rap_id' => '14',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 15,
                'guia_aprendizaje_id' => '4',
                'rap_id' => '15',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 16,
                'guia_aprendizaje_id' => '5',
                'rap_id' => '16',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 17,
                'guia_aprendizaje_id' => '6',
                'rap_id' => '17',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 18,
                'guia_aprendizaje_id' => '7',
                'rap_id' => '18',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 19,
                'guia_aprendizaje_id' => '8',
                'rap_id' => '19',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 20,
                'guia_aprendizaje_id' => '9',
                'rap_id' => '20',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 21,
                'guia_aprendizaje_id' => '9',
                'rap_id' => '21',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 22,
                'guia_aprendizaje_id' => '9',
                'rap_id' => '22',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 23,
                'guia_aprendizaje_id' => '10',
                'rap_id' => '23',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 24,
                'guia_aprendizaje_id' => '11',
                'rap_id' => '24',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 25,
                'guia_aprendizaje_id' => '12',
                'rap_id' => '25',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 26,
                'guia_aprendizaje_id' => '13',
                'rap_id' => '26',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 27,
                'guia_aprendizaje_id' => '14',
                'rap_id' => '27',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 28,
                'guia_aprendizaje_id' => '15',
                'rap_id' => '28',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 29,
                'guia_aprendizaje_id' => '16',
                'rap_id' => '29',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 30,
                'guia_aprendizaje_id' => '17',
                'rap_id' => '30',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 31,
                'guia_aprendizaje_id' => '18',
                'rap_id' => '31',
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],

        ];

        foreach ($resultados_guias as $resultado_guia) {
            GuiasResultados::create($resultado_guia);
        }
    }
}
