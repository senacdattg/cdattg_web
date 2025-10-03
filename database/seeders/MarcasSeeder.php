<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MarcasSeeder extends Seeder
{
    public function run(): void
    {
        $marcas = [
            'JHON DEERE', 'STIHL', 'HUSQVARNA', 'ECHO', 'CASE IH', 'MASSEY FERGUSON',
            'STANLEY', 'TRUPER', 'MAKITA', 'DEWALT', 'BOSCH', 'BLACK+DECKER',
            '3M', 'CATERPILLAR', 'MSA', 'DRÄGER',
            'NORMA', 'SCRIBE', 'ÉXITO', 'HP', 'EPSON', 'BROTHER', 'CANON',
            'LENOVO', 'DELL', 'ACER', 'ASUS', 'SAMSUNG', 'LG', 'HUAWEI',
            'CLOROX', 'AJAX', 'FAB', 'FAMILIA', 'COLCAFÉ', 'NESTLÉ', 'CORONA','PHILIPS',
            'APPLE', 'MICROSOFT', 'ALPINA', 'BAVARIA', 'NUTRESA', 'REXONA', 'NIVEA',
            'FABER-CASTELL', 'PILOT', 'PENTEL', 'FISKARS', 'POSTOBÓN', 'PEPSI', 'COCA-COLA',
            'RAMO', 'ADIDAS', 'NIKE', 'PUMA', 'REEBOK', 'UNDER ARMOUR', 'SKECHERS', 'TOTTO'
        ];

        foreach ($marcas as $marca) {
            DB::table('marcas')->insert([
                'nombre' => $marca,
                'user_create_id' => 1,
                'user_update_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
