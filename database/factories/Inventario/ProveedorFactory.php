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
        $ubicaciones = [
            ['departamento_id' => 11, 'municipio_id' => 100],
            ['departamento_id' => 5, 'municipio_id' => 1],
            ['departamento_id' => 50, 'municipio_id' => 113],
            ['departamento_id' => 63, 'municipio_id' => 339],
            ['departamento_id' => 73, 'municipio_id' => 432],
        ];
        $ubicacion = $this->faker->randomElement($ubicaciones);

        return [
            'proveedor' => strtoupper($this->faker->unique()->company()),
            'nit' => $this->faker->unique()->numerify('#########-#'),
            'email' => strtolower($this->faker->companyEmail()),
            'telefono' => $this->faker->numerify('60########'),
            'direccion' => $this->faker->streetAddress(),
            'departamento_id' => $ubicacion['departamento_id'],
            'municipio_id' => $ubicacion['municipio_id'],
            'contacto' => strtoupper($this->faker->name()),
            'estado_id' => 1,
            'user_create_id' => 1,
            'user_update_id' => 1,
        ];
    }
}


