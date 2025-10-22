<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AspirantesComplementariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();
        $aspirantes = [
            [
                'persona_id' => 4, // JOHN EDUARD VELEZ VASCA
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en cursos complementarios de cocina.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 5, // SAMUEL RENE YEPES RIVERA
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Busca formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 6, // DEYSON DE JESUS URREGO IBARRA
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en confección de prendas.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 7, // JUAN DAVID CASTRO VILLARREAL
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Aspira a cursos de auxiliar de cocina.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 8, // CAMILO ANDRES HERNANDEZ GONZALEZ
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en formación complementaria.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 9, // CARLOS HERNAN MOLINA ARENAS
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Busca formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 10, // NICOLAS ANTONIO ARRIETA LAGOS
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en cursos complementarios de cocina.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 11, // KAREN JULIETH GONZALEZ FERREIRA
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Aspira a formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 12, // JEFERSON ALEXANDER ALVAREZ RODRIGUEZ
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en formación complementaria.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 13, // ALEX DAVID VELANDIA PEREZ
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Busca formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 14, // RONALD BEJARANO BARBOSA
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en cursos complementarios de cocina.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 15, // JHON DEIVID ROJAS RODRIGUEZ
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Aspira a formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 16, // CRISTIAN YAHIR MONTENEGRO MORENO
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en formación complementaria.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 17, // HERNAN DAVID CIFUENTES ARENAS
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Busca formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 18, // NESTOR DAVID PORRAS SUAZA
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en cursos complementarios de cocina.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 19, // JOSE DAVID HERNANDEZ NAVAJA
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Aspira a formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 20, // HECTOR KALET CASTAÑEDA MESA
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en formación complementaria.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 21, // LUIS FELIPE CORTES MORENO
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Busca formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 22, // CRISTIAN CAMILO CAMACHO MORALES
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en cursos complementarios de cocina.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 23, // KEVIN SANTIAGO PRADA CASTELLANOS
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Aspira a formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 24, // JHON DEIBY OLARTE BENITES
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en formación complementaria.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 25, // LISSETH KATERINE RIVAS BEDOYA
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Busca formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 26, // DANIEL SANTIAGO JOVEL REYES
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en cursos complementarios de cocina.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 27, // MICHAEL ORLANDO SANTOYA PARRA
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Aspira a formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 28, // VINCEN DAVID DONCEL VELASQUEZ
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en formación complementaria.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 29, // JHON JAIRO SANTAMARIA PORRAS
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Busca formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 30, // DANNA KATHERIN VARGAS RUIZ
                'complementario_id' => 1, // COMP001 - Auxiliar de Cocina
                'observaciones' => 'Interesado en cursos complementarios de cocina.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'persona_id' => 31, // MARIO ALEXANDER CAÑOLA CANO
                'complementario_id' => 2, // COMP002 - Acabados en Madera
                'observaciones' => 'Aspira a formación en acabados de madera.',
                'estado' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        foreach ($aspirantes as $aspirante) {
            DB::table('aspirantes_complementarios')->insert($aspirante);
        }
    }
}