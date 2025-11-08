<?php

namespace Database\Seeders;

use App\Models\ResultadosCompetencia;
use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\TruncatesTables;

class ResultadosCompetenciasSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(ResultadosCompetencia::class);

        $resultadosCompetencias = [
            [
                
                'rap_id' => 1,
                'competencia_id' => 1,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 2,
                'competencia_id' => 1,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 3,
                'competencia_id' => 1,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 4,
                'competencia_id' => 1,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 5,
                'competencia_id' => 2,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 6,
                'competencia_id' => 2,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 7,
                'competencia_id' => 2,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 8,
                'competencia_id' => 3,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 9,
                'competencia_id' => 3,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 10,
                'competencia_id' => 3,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 11,
                'competencia_id' => 3,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 12,
                'competencia_id' => 4,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 13,
                'competencia_id' => 4,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 14,
                'competencia_id' => 4,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 15,
                'competencia_id' => 4,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 16,
                'competencia_id' => 5,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 17,
                'competencia_id' => 5,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 18,
                'competencia_id' => 5,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 19,
                'competencia_id' => 5,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 20,
                'competencia_id' => 6,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 21,
                'competencia_id' => 6,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 22,
                'competencia_id' => 6,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 23,
                'competencia_id' => 7,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 24,
                'competencia_id' => 7,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 25,
                'competencia_id' => 7,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 26,
                'competencia_id' => 7,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 27,
                'competencia_id' => 8,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 28,
                'competencia_id' => 8,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 29,
                'competencia_id' => 8,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 30,
                'competencia_id' => 8,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
            [
                'rap_id' => 31,
                'competencia_id' => 8,
                'user_create_id' => 1,
                'user_edit_id' => 1,
            ],
        ];

        foreach ($resultadosCompetencias as $resultadoCompetencia) {
            ResultadosCompetencia::create($resultadoCompetencia);
        }
    }
}
