<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Orden;
use App\Models\Inventario\Producto;

class DetalleOrdenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $detalles = [
            [                
                'orden_id' => 1,
                'producto_id' => 1,
                'estado_orden_id' => 46,
                'cantidad' => 1,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [                
                'orden_id' => 2,
                'producto_id' => 2,
                'estado_orden_id' => 46,
                'cantidad' => 2,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [                
                'orden_id' => 3,
                'producto_id' => 3,
                'estado_orden_id' => 46,
                'cantidad' => 5,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [                
                'orden_id' => 4,
                'producto_id' => 4,
                'estado_orden_id' => 46,
                'cantidad' => 7,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [                
                'orden_id' => 5,
                'producto_id' => 5,
                'estado_orden_id' => 46,
                'cantidad' => 1,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            
        ];
        foreach ($detalles as $detalle){
            DetalleOrden::create($detalle);
        }
    }
}
