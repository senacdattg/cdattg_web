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
        $this->truncateModel(Tema::class);
        $this->truncateTable('parametros_temas');

        // Tema 1: ESTADOS
        $tema = Tema::create([
            'id'             => 1,
            'name'           => 'ESTADOS',
            'status'         => 1,
        ]);
        $tema->parametros()->sync([
            1 => ['status' => 1],
            2 => ['status' => 1],
        ]);

        // Tema 2: TIPO DE DOCUMENTO
        $tema = Tema::create([
            'id'             => 2,
            'name'           => 'TIPO DE DOCUMENTO',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach (range(3, 8) as $id) {
            $syncData[$id] = ['status' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 3: GENERO
        $tema = Tema::create([
            'id'             => 3,
            'name'           => 'GENERO',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach ([9, 10, 11] as $id) {
            $syncData[$id] = ['status' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 4: DIAS
        $tema = Tema::create([
            'id'             => 4,
            'name'           => 'DIAS',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach (range(12, 17) as $id) {
            $syncData[$id] = ['status' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 5: MODALIDADES DE FORMACION
        $tema = Tema::create([
            'id'             => 5,
            'name'           => 'MODALIDADES DE FORMACION',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach ([18, 19, 20] as $id) {
            $syncData[$id] = ['status' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 6: NIVELES DE FORMACION
        $tema = Tema::create([
            'id'             => 6,
            'name'           => 'NIVELES DE FORMACION',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach (range(21, 24) as $id) {
            $syncData[$id] = ['status' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 7: ESTADOS DE EVIDENCIAS
        $tema = Tema::create([
            'id'             => 7,
            'name'           => 'ESTADOS DE EVIDENCIAS',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach ([25, 26, 27] as $id) {
            $syncData[$id] = ['status' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 8: TIPOS DE PRODUCTO
        $tema = Tema::create([
            'id'             => 8,
            'name'           => 'TIPOS DE PRODUCTO',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach ([28, 29] as $id) {
            $syncData[$id] = ['status' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 9: UNIDADES DE MEDIDA
        $tema = Tema::create([
            'id'             => 9,
            'name'           => 'UNIDADES DE MEDIDA',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach (range(30, 49) as $id) {
            $syncData[$id] = ['status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 10: ESTADOS DE PRODUCTO
        $tema = Tema::create([
            'id'             => 10,
            'name'           => 'ESTADOS DE PRODUCTO',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach ([50, 51] as $id) {
            $syncData[$id] = ['status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 11: TIPOS DE ORDEN
        $tema = Tema::create([
            'id'             => 11,
            'name'           => 'TIPOS DE ORDEN',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach ([52, 53] as $id) {
            $syncData[$id] = ['status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 12: ESTADOS DE ORDEN
        $tema = Tema::create([
            'id'             => 12,
            'name'           => 'ESTADOS DE ORDEN',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach ([54, 55, 56] as $id) {
            $syncData[$id] = ['status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 13: ESTADOS DE APROBACIONES
        $tema = Tema::create([
            'id'             => 13,
            'name'           => 'ESTADOS DE APROBACIONES',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach ([57, 58] as $id) {
            $syncData[$id] = ['status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 14: CATEGORÍAS
        $tema = Tema::create([
            'id'             => 14,
            'name'           => 'CATEGORÍAS',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach (range(59, 67) as $id) {
            $syncData[$id] = ['status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1];
        }
        $tema->parametros()->sync($syncData);

        // Tema 15: MARCAS
        $tema = Tema::create([
            'id'             => 15,
            'name'           => 'MARCAS',
            'status'         => 1,
        ]);
        $syncData = [];
        foreach (range(68, 187) as $id) {
            $syncData[$id] = ['status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1];
        }
        $tema->parametros()->sync($syncData);
    }
}
