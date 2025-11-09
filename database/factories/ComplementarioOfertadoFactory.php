<?php

namespace Database\Factories;

use App\Models\Ambiente;
use App\Models\ComplementarioOfertado;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ComplementarioOfertado>
 */
class ComplementarioOfertadoFactory extends Factory
{
    protected $model = ComplementarioOfertado::class;

    public function definition(): array
    {
        static $usedNombres = [];
        
        $nombres = [
            'Auxiliar de Cocina',
            'Acabados en Madera',
            'Confección de Prendas',
            'Mecánica Básica Automotriz',
            'Cultivos de Huertas Urbanas',
            'Normatividad Laboral',
        ];

        // Filtrar nombres ya usados
        $availableNombres = array_diff($nombres, $usedNombres);
        if (empty($availableNombres)) {
            $usedNombres = [];
            $availableNombres = $nombres;
        }
        
        $nombre = $availableNombres[array_rand($availableNombres)];
        $usedNombres[] = $nombre;
        
        $modalidades = [18, 19, 20];
        $jornadas = [1, 2, 3, 4];
        $ambienteId = Ambiente::query()->inRandomOrder()->value('id') ?? 1;

        return [
            'codigo' => strtoupper('COMP' . rand(100, 999)),
            'nombre' => $nombre,
            'descripcion' => 'Curso complementario de ' . strtolower($nombre) . ' diseñado para fortalecer las competencias de los aprendices.',
            'duracion' => rand(30, 80),
            'cupos' => rand(10, 30),
            'estado' => [0, 1, 2][array_rand([0, 1, 2])],
            'modalidad_id' => $modalidades[array_rand($modalidades)],
            'jornada_id' => $jornadas[array_rand($jornadas)],
            'ambiente_id' => $ambienteId,
        ];
    }
}


