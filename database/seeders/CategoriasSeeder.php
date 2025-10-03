<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriasSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            'CONSUMIBLES AGRÍCOLAS',
            'HERRAMIENTAS MANUALES',
            'MAQUINARIA Y EQUIPOS',
            'INSUMOS DE LABORATORIO',
            'OFIMÁTICA Y PAPELERÍA',
            'ASEO Y CAFETERÍA',
            'SEGURIDAD INDUSTRIAL',
            'TECNOLOGÍA',
            'MOBILIARIO'
        ];

        foreach ($categorias as $categoria) {
            DB::table('categorias')->insert([
                'nombre' => $categoria,
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
