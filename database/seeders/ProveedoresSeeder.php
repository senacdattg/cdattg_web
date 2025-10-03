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
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proveedor' => 'DISTRIBUCIONES LA BODEGA',
                'nit' => '890765432-1',
                'email' => 'ventas@labodega.com',
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proveedor' => 'FERTILIZANTES VERDES LTDA.',
                'nit' => '900456789-0',
                'email' => 'info@fertilizantesverdes.co',
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proveedor' => 'SEMILLAS Y AGROQUÍMICOS EL CAMPO',
                'nit' => '800123456-7',
                'email' => 'campo@semillasagro.com',
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'proveedor' => 'PROVEEDOR INDEPENDIENTE JUAN PÉREZ',
                'nit' => '1122334455',
                'email' => 'juanperez@gmail.com',
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
