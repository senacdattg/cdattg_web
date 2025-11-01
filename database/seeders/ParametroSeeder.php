<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parametro; // Asegúrate de usar la notación correcta

class ParametroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $parametros = [
            // Estados
            ['id' => 1, 'name' => 'ACTIVO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 2, 'name' => 'INACTIVO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Tipos de documento
            ['id' => 3, 'name' => 'CEDULA DE CIUDADANIA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 4, 'name' => 'CEDULA DE EXTRANJERIA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 5, 'name' => 'PASAPORTE', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 6, 'name' => 'TARJETA DE IDENTIDAD', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 7, 'name' => 'REGISTRO CIVIL', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 8, 'name' => 'SIN IDENTIFICACION', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Género
            ['id' => 9, 'name' => 'MASCULINO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 10, 'name' => 'FEMENINO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 11, 'name' => 'NO DEFINE', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Dias
            ['id' => 12, 'name' => 'LUNES', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 13, 'name' => 'MARTES', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 14, 'name' => 'MIERCOLES', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 15, 'name' => 'JUEVES', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 16, 'name' => 'VIERNES', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 17, 'name' => 'SABADO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Modalidades
            ['id' => 18, 'name' => 'PRESENCIAL', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 19, 'name' => 'VIRTUAL', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 20, 'name' => 'MIXTA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Niveles de formación
            ['id' => 21, 'name' => 'TÉCNICO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 22, 'name' => 'TECNÓLOGO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 23, 'name' => 'AUXILIAR', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 24, 'name' => 'OPERARIO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Estados de evidencias
            ['id' => 25, 'name' => 'PENDIENTE', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 26, 'name' => 'EN CURSO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 27, 'name' => 'COMPLETADO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Tipos de producto
            ['id' => 28, 'name' => 'CONSUMIBLE', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 29, 'name' => 'NO CONSUMIBLE', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Unidades de medida
            ['id' => 30, 'name' => 'GRAMOS', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 31, 'name' => 'LIBRAS', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 32, 'name' => 'KILOGRAMOS', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 33, 'name' => 'ARROBA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 34, 'name' => 'QUINTAL', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 35, 'name' => 'ONZA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 36, 'name' => 'MILILITRO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 37, 'name' => 'LITRO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 38, 'name' => 'GALÓN', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 39, 'name' => 'ONZA LÍQUIDA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 40, 'name' => 'BARRIL', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 41, 'name' => 'UNIDADES', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            
            // Estados de producto
            ['id' => 42, 'name' => 'DISPONIBLE', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 43, 'name' => 'AGOTADO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Tipos de orden
            ['id' => 44, 'name' => 'PRÉSTAMO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 45, 'name' => 'SALIDA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Estados de orden
            ['id' => 46, 'name' => 'EN ESPERA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 47, 'name' => 'APROBADA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 48, 'name' => 'RECHAZADA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            
            // Estados de aprobaciones
            ['id' => 49, 'name' => 'ENTREGADA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 50, 'name' => 'EN PRÉSTAMO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Categorías
            ['id' => 51, 'name' => 'CONSUMIBLES AGRÍCOLAS', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 52, 'name' => 'HERRAMIENTAS MANUALES', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 53, 'name' => 'MAQUINARIA Y EQUIPOS', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 54, 'name' => 'INSUMOS DE LABORATORIO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 55, 'name' => 'OFIMÁTICA Y PAPELERÍA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 56, 'name' => 'ASEO Y CAFETERÍA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 57, 'name' => 'SEGURIDAD INDUSTRIAL', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 58, 'name' => 'TECNOLOGÍA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 59, 'name' => 'MOBILIARIO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],

            // Marcas
            ['id' => 60, 'name' => 'LENOVO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 61, 'name' => 'DELL', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 62, 'name' => 'ACER', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 63, 'name' => 'ASUS', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 64, 'name' => 'HP', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 65, 'name' => 'SAMSUNG', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 66, 'name' => 'LG', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 67, 'name' => 'HUAWEI', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 68, 'name' => 'EPSON', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1], 
            ['id' => 69, 'name' => 'MICROSOFT', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 70, 'name' => 'ADIDAS', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 71, 'name' => 'NIKE', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 72, 'name' => 'PUMA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 73, 'name' => 'REEBOK', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 74, 'name' => 'SKECHERS', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 75, 'name' => 'TOTTO', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            ['id' => 76, 'name' => 'NORMA', 'status' => 1, 'user_create_id' => 1, 'user_edit_id' => 1],
            

        ];

        foreach ($parametros as $parametro) {
            Parametro::create($parametro);
        }
    }
}
