<?php

namespace Database\Factories\Inventario;

use App\Models\Ambiente;
use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\Producto;
use App\Models\Inventario\Proveedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Inventario\Producto>
 */
class ProductoFactory extends Factory
{
    protected $model = Producto::class;

    public function definition(): array
    {
        $ambienteId = Ambiente::query()->inRandomOrder()->value('id') ?? 1;

        return [
            'producto' => strtoupper($this->faker->words(3, true)),
            'tipo_producto_id' => $this->faker->randomElement([28, 29]),
            'descripcion' => $this->faker->sentence(8),
            'peso' => $this->faker->randomFloat(2, 0.5, 2500),
            'unidad_medida_id' => $this->faker->randomElement(range(30, 41)),
            'cantidad' => $this->faker->numberBetween(1, 80),
            'codigo_barras' => $this->faker->unique()->ean13(),
            'estado_producto_id' => $this->faker->randomElement([42, 43]),
            'categoria_id' => $this->faker->randomElement(range(51, 59)),
            'marca_id' => $this->faker->numberBetween(60, 179),
            'contrato_convenio_id' => ContratoConvenio::factory(),
            'ambiente_id' => $ambienteId,
            'proveedor_id' => Proveedor::factory(),
            'fecha_vencimiento' => $this->faker->dateTimeBetween('+3 months', '+2 years')->format('Y-m-d'),
            'imagen' => 'img/inventario/producto-default.png',
            'user_create_id' => 1,
            'user_update_id' => 1,
        ];
    }
}


