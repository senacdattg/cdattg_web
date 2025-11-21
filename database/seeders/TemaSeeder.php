<?php

namespace Database\Seeders;

use App\Models\Tema;
use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\TruncatesTables;

class TemaSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->resetTables();

        foreach ($this->temasConfig() as $config) {
            $this->createTemaWithParametros(
                $config['id'],
                $config['name'],
                $config['paramIds']
            );
        }
    }

    private function resetTables(): void
    {
        if (app()->environment('production')) {
            // Evitar truncados en producción protege registros históricos; seeder usará update-or-create.
            return;
        }

        $this->truncateModel(Tema::class);
        $this->truncateTable('parametros_temas');
    }

    /**
     * Define la configuración estática de los temas y sus parámetros asociados.
     *
     * Centralizar la definición facilita mantenimiento y evita duplicidad.
     */
    private function temasConfig(): array
    {
        return [
            [
                'id'       => 1,
                'name'     => 'ESTADOS',
                'paramIds' => [1, 2],
            ],
            [
                'id'       => 2,
                'name'     => 'TIPO DE DOCUMENTO',
                'paramIds' => array_merge(range(3, 8), range(274, 275)),
            ],
            [
                'id'       => 3,
                'name'     => 'GENERO',
                'paramIds' => [9, 10, 11],
            ],
            [
                'id'       => 4,
                'name'     => 'DIAS',
                'paramIds' => [range(12, 18), [276]],
            ],
            [
                'id'       => 5,
                'name'     => 'MODALIDADES DE FORMACION',
                'paramIds' => [18, 19, 20],
            ],
            [
                'id'       => 6,
                'name'     => 'NIVELES DE FORMACION',
                'paramIds' => range(21, 24),
            ],
            [
                'id'       => 7,
                'name'     => 'ESTADOS DE EVIDENCIAS',
                'paramIds' => [25, 26, 27],
            ],
            [
                'id'       => 8,
                'name'     => 'TIPOS DE PRODUCTO',
                'paramIds' => [28, 29],
            ],
            [
                'id'       => 9,
                'name'     => 'UNIDADES DE MEDIDA',
                'paramIds' => range(30, 49),
            ],
            [
                'id'       => 10,
                'name'     => 'ESTADOS DE PRODUCTO',
                'paramIds' => [50, 51],
            ],
            [
                'id'       => 11,
                'name'     => 'TIPOS DE ORDEN',
                'paramIds' => [52, 53],
            ],
            [
                'id'       => 12,
                'name'     => 'ESTADOS DE ORDEN',
                'paramIds' => [54, 55, 56],
            ],
            [
                'id'       => 13,
                'name'     => 'ESTADOS DE APROBACIONES',
                'paramIds' => [57, 58],
            ],
            [
                'id'       => 14,
                'name'     => 'CATEGORÍAS',
                'paramIds' => range(59, 67),
            ],
            [
                'id'       => 15,
                'name'     => 'MARCAS',
                'paramIds' => range(68, 187),
            ],
            [
                'id'       => 16,
                'name'     => 'PERSONA CARACTERIZACION',
                'paramIds' => range(188, 235),
            ],
            [
                'id'       => 17,
                'name'     => 'VÍAS',
                'paramIds' => range(236, 247),
            ],
            [
                'id'       => 18,
                'name'     => 'LETRAS',
                'paramIds' => range(248, 273),
            ],
            [
                'id'       => 19,
                'name'     => 'JORNADAS',
                'paramIds' => range(277, 280),
            ],
            [
                'id'       => 20,
                'name'     => 'TIPOS DE VINCULACION',
                'paramIds' => range(281, 283),
            ],
            [
                'id'       => 21,
                'name'     => 'NIVELES ACADEMICOS',
                'paramIds' => range(284, 292),
            ],
        ];
    }

    private function createTemaWithParametros(int $id, string $name, array $paramIds): void
    {
        $tema = Tema::query()->updateOrCreate(
            ['id' => $id],
            [
                'name'           => $name,
                'status'         => 1,
                'user_create_id' => null,
                'user_edit_id'   => null,
            ]
        );

        $tema->parametros()->sync($this->buildSyncData($paramIds));
    }

    /**
     * Construye el arreglo de sincronización respetando el formato requerido por la relación.
     */
    private function buildSyncData(array $paramIds): array
    {
        $syncData = [];

        foreach ($paramIds as $paramId) {
            if (is_array($paramId)) {
                foreach ($paramId as $nestedId) {
                    $syncData[$nestedId] = ['status' => 1];
                }
                continue;
            }

            $syncData[$paramId] = ['status' => 1];
        }

        return $syncData;
    }
}
