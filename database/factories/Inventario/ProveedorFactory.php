<?php

namespace Database\Factories\Inventario;

use App\Models\Inventario\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventario\Proveedor>
 */
class ProveedorFactory extends Factory
{
    protected $model = Proveedor::class;

    public function definition(): array
    {
        static $usedNits = [];
        
        $ubicaciones = [
            ['departamento_id' => 11, 'municipio_id' => 100],
            ['departamento_id' => 5, 'municipio_id' => 1],
            ['departamento_id' => 50, 'municipio_id' => 113],
            ['departamento_id' => 63, 'municipio_id' => 339],
            ['departamento_id' => 73, 'municipio_id' => 432],
        ];
        $ubicacion = $ubicaciones[array_rand($ubicaciones)];

        $empresas = ['TECNOLOGÍA', 'SISTEMAS', 'SUMINISTROS', 'EQUIPOS', 'COMERCIAL', 'DISTRIBUCIONES', 'IMPORTADORA', 'SOLUCIONES'];
        $sufijos = ['LTDA', 'S.A.S', 'S.A', 'E.U'];
        $proveedor = strtoupper($empresas[array_rand($empresas)] . ' ' . $empresas[array_rand($empresas)] . ' ' . $sufijos[array_rand($sufijos)]);
        
        // Generar NIT único
        do {
            $nit = rand(100000000, 999999999) . '-' . rand(0, 9);
        } while (in_array($nit, $usedNits));
        $usedNits[] = $nit;

        $nombres = ['Carlos', 'Ana', 'Luis', 'María', 'Jorge', 'Sofía', 'Pedro', 'Laura'];
        $apellidos = ['García', 'López', 'Martínez', 'Rodríguez', 'González'];
        $contacto = strtoupper($nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)]);

        return [
            'proveedor' => $proveedor,
            'nit' => $nit,
            'email' => strtolower('contacto' . rand(100, 999) . '@' . str_replace(' ', '', strtolower($empresas[array_rand($empresas)])) . '.com'),
            'telefono' => '60' . rand(10000000, 99999999),
            'direccion' => 'Calle ' . rand(1, 100) . ' #' . rand(1, 50) . '-' . rand(1, 99),
            'departamento_id' => $ubicacion['departamento_id'],
            'municipio_id' => $ubicacion['municipio_id'],
            'contacto' => $contacto,
            'estado_id' => 1,
            'user_create_id' => 1,
            'user_update_id' => 1,
        ];
    }
}


