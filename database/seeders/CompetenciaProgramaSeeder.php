<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompetenciaPrograma;

class CompetenciaProgramaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $competencias_programas = [
            [
                'id' => 1,
                'programa_id' => 1,
                'competencia_id' => 1,
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 2,
                'programa_id' =>1,
                'competencia_id' => 2,
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 3,
                'programa_id' => 1,
                'competencia_id' => 3,
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 4,
                'programa_id' => 1,
                'competencia_id' => 4,
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 5,
                'programa_id' => 1,
                'competencia_id' => 5,
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 6,
                'programa_id' => 1,
                'competencia_id' => 6,
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 7,
                'programa_id' => 1,
                'competencia_id' => 7,
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
            [
                'id' => 8,
                'programa_id' => 1,
                'competencia_id' => 8,
                'user_create_id' => 1,
                'user_edit_id' => 1,    
            ],
        ];

        CompetenciaPrograma::insert($competencias_programas);
    }
}
