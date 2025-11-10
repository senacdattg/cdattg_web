<?php

namespace Database\Factories;

use App\Models\Departamento;
use App\Models\Pais;
use App\Models\Parametro;
use App\Models\ProgramaFormacion;
use App\Models\RedConocimiento;
use App\Models\Regional;
use App\Models\User;
use App\Models\Competencia;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ProgramaFormacion>
 */
class ProgramaFormacionFactory extends Factory
{
    protected $model = ProgramaFormacion::class;

    public function configure(): static
    {
        return $this->afterCreating(function (ProgramaFormacion $programa) {
            $competenciasIds = Competencia::query()
                ->inRandomOrder()
                ->limit($this->faker->numberBetween(1, 3))
                ->pluck('id')
                ->all();

            if (!empty($competenciasIds)) {
                $programa->competencias()->sync($competenciasIds);
            }
        });
    }

    public function definition(): array
    {
        $horasTotales = $this->faker->numberBetween(800, 2200);
        $horasEtapaLectiva = $this->faker->numberBetween(400, $horasTotales - 200);
        $horasEtapaProductiva = $horasTotales - $horasEtapaLectiva;

        $redConocimientoId = $this->obtenerRedConocimientoId();
        $nivelFormacionId = $this->obtenerNivelFormacionId();

        return [
            'codigo' => (string) $this->faker->unique()->numberBetween(100000, 999999),
            'nombre' => strtoupper($this->faker->unique()->sentence(3)),
            'red_conocimiento_id' => $redConocimientoId,
            'nivel_formacion_id' => $nivelFormacionId,
            'horas_totales' => $horasTotales,
            'horas_etapa_lectiva' => $horasEtapaLectiva,
            'horas_etapa_productiva' => $horasEtapaProductiva,
            'status' => true,
            'user_create_id' => User::query()->value('id') ?? User::factory()->create()->id,
            'user_edit_id' => User::query()->value('id') ?? User::factory()->create()->id,
        ];
    }

    private function obtenerRedConocimientoId(): int
    {
        $redConocimiento = RedConocimiento::query()->inRandomOrder()->first();

        if ($redConocimiento) {
            return $redConocimiento->id;
        }

        $regionalId = Regional::query()->value('id') ?? $this->crearRegional();

        $usuarioId = User::query()->value('id') ?? User::factory()->create()->id;

        return RedConocimiento::query()->create([
            'nombre' => 'RED ' . Str::upper(Str::random(8)),
            'regionals_id' => $regionalId,
            'user_create_id' => $usuarioId,
            'user_edit_id' => $usuarioId,
            'status' => true,
        ])->id;
    }

    private function obtenerNivelFormacionId(): int
    {
        $nivel = Parametro::query()
            ->whereIn('name', ['TÉCNICO', 'TECNÓLOGO', 'AUXILIAR', 'OPERARIO'])
            ->inRandomOrder()
            ->first();

        if ($nivel) {
            return $nivel->id;
        }

        return Parametro::query()->create([
            'name' => 'TÉCNICO',
            'status' => 1,
        ])->id;
    }

    private function crearRegional(): int
    {
        $departamentoId = Departamento::query()->value('id') ?? $this->crearDepartamento();

        return Regional::query()->create([
            'nombre' => 'REGIONAL ' . Str::upper(Str::random(6)),
            'departamento_id' => $departamentoId,
            'status' => 1,
        ])->id;
    }

    private function crearDepartamento(): int
    {
        $paisId = Pais::query()->value('id') ?? Pais::query()->create([
            'pais' => 'COLOMBIA',
            'status' => 1,
        ])->id;

        return Departamento::query()->create([
            'departamento' => 'CUNDINAMARCA ' . Str::upper(Str::random(4)),
            'pais_id' => $paisId,
            'status' => 1,
        ])->id;
    }
}

