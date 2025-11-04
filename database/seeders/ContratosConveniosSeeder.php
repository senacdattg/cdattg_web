<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContratosConveniosSeeder extends Seeder
{
    public function run(): void
    {
        $contratos = [
            ['nombre' => 'CONVENIO SENA - GOBERNACIÓN DEL GUAVIARE', 'codigo' => '045-2024'],        
            ['nombre' => 'CONVENIO SENA - ALCALDÍA DE SAN JOSÉ DEL GUAVIARE', 'codigo' => '102-2025'],
            ['nombre' => 'CONVENIO SENA - MINISTERIO DE AGRICULTURA', 'codigo' => '256-2025'],
            ['nombre' => 'CONVENIO SENA - UNIVERSIDAD NACIONAL', 'codigo' => '310-2025'],
            ['nombre' => 'CONVENIO SENA - CORPOAMAZONIA', 'codigo' => '478-2025'],
            ['nombre' => 'PROYECTO AGROSENA 2025', 'codigo' => 'AGRO-25'],
            ['nombre' => 'PROGRAMA JÓVENES RURALES EMPRENDEDORES', 'codigo' => 'JRE-25'],
            ['nombre' => 'PROYECTO INNOVACIÓN AGROINDUSTRIAL CDATTG', 'codigo' => 'INNOVA-25'],
            ['nombre' => 'FONDO EMPRENDER - AGROINDUSTRIA', 'codigo' => 'FE-25'],
            ['nombre' => 'PROGRAMA PRODUCCIÓN SOSTENIBLE', 'codigo' => 'SOST-25'],
        ];

        foreach ($contratos as $contrato) {
            DB::table('contratos_convenios')->insert([
                'name' => $contrato['nombre'],
                'codigo' => $contrato['codigo'],
                'fecha_inicio' => now(),
                'fecha_fin' => now()->addYear(),
                'proveedor_id' => 1, 
                'estado_id' => 1,    
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
