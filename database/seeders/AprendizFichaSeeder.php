<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AprendizFicha;
use App\Models\Aprendiz;

class AprendizFichaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aprendices = Aprendiz::where('id', '>=', '1')->get();

        foreach ($aprendices as $aprendiz) {
            AprendizFicha::create([
                'aprendiz_id' => $aprendiz->id,
                'ficha_id' => 1
            ]);
        }
    }
}
