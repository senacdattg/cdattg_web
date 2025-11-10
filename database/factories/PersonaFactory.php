<?php

namespace Database\Factories;

use App\Models\Persona;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Persona>
 */
class PersonaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $genero = [9, 10, 11][array_rand([9, 10, 11])];
        
        $nombresMasculinos = ['Carlos', 'Juan', 'Pedro', 'Luis', 'Miguel', 'José', 'Andrés', 'Jorge', 'Diego', 'Fernando'];
        $nombresFemeninos = ['María', 'Ana', 'Carmen', 'Laura', 'Sofía', 'Valentina', 'Lucía', 'Isabella', 'Camila', 'Daniela'];
        $nombresNeutros = ['Alex', 'Taylor', 'Jordan', 'Casey', 'Morgan', 'Riley', 'Skyler', 'Avery', 'Quinn', 'Jamie'];
        
        $primerNombre = match ($genero) {
            9 => $nombresMasculinos[array_rand($nombresMasculinos)],
            10 => $nombresFemeninos[array_rand($nombresFemeninos)],
            default => $nombresNeutros[array_rand($nombresNeutros)],
        };

        $apellidos = ['García', 'Rodríguez', 'González', 'Fernández', 'López', 'Martínez', 'Sánchez', 'Pérez', 'Gómez', 'Ramírez', 'Torres', 'Flores', 'Rivera', 'Silva', 'Morales'];

        $ubicaciones = [
            ['departamento_id' => 95, 'municipio_id' => 824],
            ['departamento_id' => 11, 'municipio_id' => 100],
            ['departamento_id' => 25, 'municipio_id' => 126],
            ['departamento_id' => 50, 'municipio_id' => 113],
            ['departamento_id' => 63, 'municipio_id' => 339],
            ['departamento_id' => 5, 'municipio_id' => 1],
            ['departamento_id' => 73, 'municipio_id' => 432],
        ];
        $ubicacion = $ubicaciones[array_rand($ubicaciones)];

        $numeroDocumento = str_pad(rand(100000000, 9999999999), 10, '0', STR_PAD_LEFT);
        $timestamp = time();
        $email = strtolower($primerNombre) . rand(1000, 9999) . '@example.com';

        return [
            'tipo_documento' => [3, 4, 5, 6][array_rand([3, 4, 5, 6])],
            'numero_documento' => $numeroDocumento . $timestamp,
            'primer_nombre' => $primerNombre,
            'segundo_nombre' => (rand(1, 100) <= 50) ? ($genero == 9 ? $nombresMasculinos[array_rand($nombresMasculinos)] : $nombresFemeninos[array_rand($nombresFemeninos)]) : null,
            'primer_apellido' => $apellidos[array_rand($apellidos)],
            'segundo_apellido' => (rand(1, 100) <= 50) ? $apellidos[array_rand($apellidos)] : null,
            'fecha_nacimiento' => date('Y-m-d', strtotime('-' . rand(20, 55) . ' years -' . rand(0, 365) . ' days')),
            'genero' => $genero,
            'telefono' => (rand(1, 100) <= 60) ? '60' . rand(10000000, 99999999) : null,
            'celular' => '3' . rand(100000000, 999999999),
            'email' => $email,
            'pais_id' => 1,
            'departamento_id' => $ubicacion['departamento_id'],
            'municipio_id' => $ubicacion['municipio_id'],
            'direccion' => 'Calle ' . rand(1, 100) . ' #' . rand(1, 50) . '-' . rand(1, 99),
            'status' => 1,
            'user_create_id' => 1,
            'user_edit_id' => 1,
        ];
    }
}
