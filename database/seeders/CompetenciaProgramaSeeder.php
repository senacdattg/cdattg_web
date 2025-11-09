<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompetenciaPrograma;
use Database\Seeders\Concerns\TruncatesTables;

class CompetenciaProgramaSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(CompetenciaPrograma::class);

        $competencias_programas = [
            // Programa 1 - Todas las competencias principales
            ['programa_id' => 1, 'competencia_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 2, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 3, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 6, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 8, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 9, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 10, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 11, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 1, 'competencia_id' => 12, 'user_create_id' => 1, 'user_edit_id' => 1],
            
            // Programa 2 - Competencias técnicas
            ['programa_id' => 2, 'competencia_id' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 2, 'competencia_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 2, 'competencia_id' => 5, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 2, 'competencia_id' => 7, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 2, 'competencia_id' => 8, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 2, 'competencia_id' => 12, 'user_create_id' => 1, 'user_edit_id' => 1],
            
            // Programa 3 - Competencias básicas
            ['programa_id' => 3, 'competencia_id' => 3, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 3, 'competencia_id' => 4, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['programa_id' => 3, 'competencia_id' => 11, 'user_create_id' => 1, 'user_edit_id' => 1],
        ];

        foreach ($competencias_programas as $relacion) {
            CompetenciaPrograma::create($relacion);
        }

        $this->command->info('✓ Relaciones Competencia-Programa creadas exitosamente.');
    }
}
