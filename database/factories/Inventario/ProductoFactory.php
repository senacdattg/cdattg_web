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

        $productos = ['COMPUTADOR', 'MONITOR', 'TECLADO', 'MOUSE', 'CABLE', 'SWITCH', 'ROUTER', 'ESCRITORIO', 'SILLA'];
        $producto = strtoupper($productos[array_rand($productos)] . ' ' . $productos[array_rand($productos)]);
        
        $descripciones = [
            'Producto de alta calidad para uso en ambientes formativos',
            'Equipo tecnológico para la formación profesional',
            'Material didáctico para el desarrollo de competencias',
            'Herramienta especializada para uso institucional',
        ];

        return [
            'producto' => $producto,
            'tipo_producto_id' => [28, 29][array_rand([28, 29])],
            'descripcion' => $descripciones[array_rand($descripciones)],
            'peso' => round(rand(50, 250000) / 100, 2),
            'unidad_medida_id' => range(30, 41)[array_rand(range(30, 41))],
            'cantidad' => rand(1, 80),
            'codigo_barras' => rand(1000000000000, 9999999999999),
            'estado_producto_id' => [42, 43][array_rand([42, 43])],
            'categoria_id' => range(51, 59)[array_rand(range(51, 59))],
            'marca_id' => rand(60, 179),
            'contrato_convenio_id' => ContratoConvenio::factory(),
            'ambiente_id' => $ambienteId,
            'proveedor_id' => Proveedor::factory(),
            'fecha_vencimiento' => date('Y-m-d', strtotime('+' . rand(90, 730) . ' days')),
            'imagen' => 'img/inventario/producto-default.png',
            'user_create_id' => 1,
            'user_update_id' => 1,
        ];
    }
}


