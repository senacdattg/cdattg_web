<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JornadaFormacion;
use Database\Seeders\Concerns\TruncatesTables;

class JornadaFormacionSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(JornadaFormacion::class);

        $jornadas = [
            [
                'jornada' => 'MAÑANA'
            ],
            [
                'jornada' => 'TARDE'
            ],
            [
                'jornada' => 'NOCHE'
            ],
            [
                'jornada' => 'FINES DE SEMANA'
            ]
        ];

        foreach ($jornadas as $jornada) {
            JornadaFormacion::create($jornada);
        }
    }
}
