<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventario\Aprobacion;

class AprobacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aprobaciones = [
            [                
                'detalle_orden_id' => 1,
                'estado_aprobacion_id' => 50,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [                
                'detalle_orden_id' => 2,
                'estado_aprobacion_id' => 49,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],
            [                
                'detalle_orden_id' => 3,
                'estado_aprobacion_id' => 50,
                'user_create_id' => 1,
                'user_update_id' => 1,
            ],

        ];
        foreach ($aprobaciones as $aprobacion){
            Aprobacion::create($aprobacion);
        }
    }
}
