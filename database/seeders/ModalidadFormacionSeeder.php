<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ModalidadFormacion;

class ModalidadFormacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modalidades_formacion = [
            'PRESENCIAL',
            'VIRTUAL',
            'MIXTA',
        ];

        foreach ($modalidades_formacion as $modalidad) {
            ModalidadFormacion::create([
                'modalidad_formacion' => $modalidad
            ]);
        }
    }
}
