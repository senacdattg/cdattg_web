<?php

namespace Database\Factories;

use App\Models\Instructor;
use App\Models\Persona;
use App\Models\Regional;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Collection;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Instructor>
 */
class InstructorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Instructor::class;

    public function definition(): array
    {
        $especialidades = [
            'Tecnologías de la Información y las Comunicaciones',
            'Electrónica',
            'Mecánica Industrial',
            'Construcción',
            'Gastronomía',
        ];

        $principal = $this->faker->randomElement($especialidades);
        $secundarias = Collection::make($especialidades)
            ->reject(fn ($value) => $value === $principal)
            ->shuffle()
            ->take($this->faker->numberBetween(0, 2))
            ->values()
            ->all();

        $competencias = [
            'Programación Web',
            'Bases de Datos',
            'Automatización Industrial',
            'Gestión de Proyectos',
            'Diseño Gráfico',
            'Seguridad Informática',
            'Analítica de Datos',
            'Redes de Computadores',
        ];

        $regionalId = Regional::query()->inRandomOrder()->value('id') ?? 1;

        return [
            'persona_id' => Persona::factory(),
            'regional_id' => $regionalId,
            'status' => $this->faker->boolean(85),
            'user_create_id' => 1,
            'user_edit_id' => 1,
            'especialidades' => [
                'principal' => $principal,
                'secundarias' => $secundarias,
            ],
            'competencias' => $this->faker->randomElements($competencias, $this->faker->numberBetween(2, 4)),
            'anos_experiencia' => $this->faker->numberBetween(2, 25),
            'experiencia_laboral' => $this->faker->paragraph(3),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Instructor $instructor) {
            $persona = $instructor->persona;

            if (! $persona) {
                return;
            }

            $email = strtolower($persona->email);

            if (! $persona->user) {
                $user = User::factory()
                    ->forPersona($persona)
                    ->state([
                        'email' => $email,
                        'status' => $instructor->status ? 1 : 0,
                    ])
                    ->create();

                if (! $user->hasRole('INSTRUCTOR')) {
                    $user->assignRole('INSTRUCTOR');
                }
            } else {
                $persona->user->syncRoles(['INSTRUCTOR']);
                $persona->user->update(['status' => $instructor->status ? 1 : 0]);
            }
        });
    }

    public function createdBy(int $userId): static
    {
        return $this->state(fn () => [
            'user_create_id' => $userId,
            'user_edit_id' => $userId,
        ]);
    }
}
