<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProveedoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('proveedores')->insert([
            [
                'proveedor' => 'AGROINSUMOS DEL LLANO S.A.S',                
                'nit' => '901234567-8',
                'email' => 'contacto@agroinsumosllano.com',
                'telefono' => '1234567890',
                'direccion' => 'Calle 123 #45-67, ',
                'municipio_id' => 113,
                'contacto' => 'CARLOS PEREZ',
                'estado_id' => 1,
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proveedor' => 'DISTRIBUCIONES LA BODEGA',
                'nit' => '890765432-1',
                'email' => 'ventas@labodega.com',
                'telefono' => '0987654321',
                'direccion' => 'Calle 08 #24-67, ',
                'municipio_id' =>1.070 ,
                'contacto' => 'ANGELA MARTÍNEZ',
                'estado_id' => 1,
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proveedor' => 'FERTILIZANTES VERDES LTDA.',
                'nit' => '900456789-0',
                'email' => 'info@fertilizantesverdes.co',
                'telefono' => '1122334455',
                'direccion' => 'carretera 26 #18-28, ',
                'municipio_id' =>339,
                'contacto' => 'ANA ROBERTA',
                'estado_id' => 1,
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proveedor' => 'SEMILLAS Y AGROQUÍMICOS EL CAMPO',
                'nit' => '800123456-7',
                'email' => 'campo@semillasagro.com',
                'telefono' => '1122334556',
                'direccion' => 'carretera 24 #41-16, ',
                'municipio_id' =>334,
                'contacto' => 'ALVARO LOPEZ',
                'estado_id' => 1,
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proveedor' => 'PROVEEDOR INDEPENDIENTE JUAN PÉREZ',
                'nit' => '1122334455',
                'email' => 'juanperez@gmail.com',
                'telefono' => '1122334455',
                'direccion' => 'carretera 12 #17-59, ',
                'municipio_id' =>432,
                'contacto' => ' JUAN PÉREZ',
                'estado_id' => 1,
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
