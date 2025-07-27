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
        ];

        foreach ($parametros as $parametro) {
            Parametro::create($parametro);
        }
    }
}
