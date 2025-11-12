<?php

namespace Database\Factories;

use App\Models\Competencia;
use App\Models\CompetenciaPrograma;
use App\Models\ProgramaFormacion;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\CompetenciaPrograma>
 */
class CompetenciaProgramaFactory extends Factory
{
    protected $model = CompetenciaPrograma::class;

    public function definition(): array
    {
        $competencia = Competencia::inRandomOrder()->first();
        if (! $competencia) {
            $competencia = Competencia::factory()->create();
        }

        $programa = ProgramaFormacion::inRandomOrder()->first();
        if (! $programa) {
            $programa = ProgramaFormacion::factory()->create();
        }

        $usuario = User::inRandomOrder()->first();
        if (! $usuario) {
            $usuario = User::factory()->create();
        }

        return [
            'competencia_id' => $competencia->id,
            'programa_id' => $programa->id,
            'user_create_id' => $usuario->id,
            'user_edit_id' => $usuario->id,
        ];
    }
}
