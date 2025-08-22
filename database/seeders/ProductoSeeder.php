<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Inventario\Producto;

class ProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            [
                'producto' => 'PRODUCTO 1',
                'tipo_producto_id' => 28,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 1',
                'peso' => 15,
                'unidad_medida_id' => 31,
                'cantidad' => 4,
                'codigo_barras' => 1234567890123,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
            [
                'producto' => 'PRODUCTO 2',
                'tipo_producto_id' => 28,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 2',
                'peso' => 20,
                'unidad_medida_id' => 32,
                'cantidad' => 8,
                'codigo_barras' => 987654321245,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
            [
                'producto' => 'PRODUCTO 3',
                'tipo_producto_id' => 28,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 3',
                'peso' => 500,
                'unidad_medida_id' => 30,
                'cantidad' => 50,
                'codigo_barras' => 9849302856754,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
            [
                'producto' => 'PRODUCTO 4',
                'tipo_producto_id' => 28,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 4',
                'peso' => 1000,
                'unidad_medida_id' => 30,
                'cantidad' => 32,
                'codigo_barras' => 9843012348596,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
            [
                'producto' => 'PRODUCTO 5',
                'tipo_producto_id' => 29,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 5',
                'peso' => 20,
                'unidad_medida_id' => 41,
                'cantidad' => 6,
                'codigo_barras' => 1928374506978,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
            [
                'producto' => 'PRODUCTO 6',
                'tipo_producto_id' => 28,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 6',
                'peso' => 100,
                'unidad_medida_id' => 36,
                'cantidad' => 9,
                'codigo_barras' => 9388745667548,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
            [
                'producto' => 'PRODUCTO 7',
                'tipo_producto_id' => 29,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 7',
                'peso' => 11,
                'unidad_medida_id' => 41,
                'cantidad' => 10,
                'codigo_barras' => 9982234442147,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
            [
                'producto' => 'PRODUCTO 8',
                'tipo_producto_id' => 28,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 8',
                'peso' => 2000,
                'unidad_medida_id' => 30,
                'cantidad' => 35,
                'codigo_barras' => 2229998887334,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
            [
                'producto' => 'PRODUCTO 9',
                'tipo_producto_id' => 28,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 9',
                'peso' => 1.5,
                'unidad_medida_id' => 37,
                'cantidad' => 29,
                'codigo_barras' => 4448885556660,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
            [
                'producto' => 'PRODUCTO 10',
                'tipo_producto_id' => 28,
                'descripcion' => 'DESCRIPCIÓN DE PRUEBA DEL PRODUCTO 10',
                'peso' => 2,
                'unidad_medida_id' => 32,
                'cantidad' => 10,
                'codigo_barras' => 7374859432957,
                'estado_producto_id' => 42,
                'imagen' => 'img/inventario/imagen_default.png',
                'user_create_id' => 1, 
                'user_update_id' => 1,
            ],
        ];
        foreach ($productos as $producto){
            Producto::create($producto);
        }
    }
}
