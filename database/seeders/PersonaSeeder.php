<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Factories\PersonaFactory;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Persona::create([
            'id' => 1,
            'tipo_documento' => NULL,
            'numero_documento' => 987654321,
            'primer_nombre' => 'SUPER',
            'segundo_nombre' => NULL,
            'primer_apellido' => 'ADMINISTRADOR',
            'segundo_apellido' => NULL,
            'fecha_nacimiento' => '2000-01-01',
            'genero' => NULL,
            'telefono' => NULL,
            'celular' => NULL,
            'email' => 'superAdmin@superAdmin.com',
            'pais_id' => 1,
            'departamento_id' => 95,
            'municipio_id' => 824,
            'direccion' => 'CALLE FALSA 123',
            'status' => 1,
        ]);

        Persona::create([
            'id' => 2,
            'tipo_documento' => NULL,
            'numero_documento' => 654321123,
            'primer_nombre' => 'ADMIN',
            'segundo_nombre' => NULL,
            'primer_apellido' => 'PRUEBA',
            'segundo_apellido' => NULL,
            'fecha_nacimiento' => '2000-01-01',
            'genero' => NULL,
            'telefono' => NULL,
            'celular' => NULL,
            'email' => 'admin@admin.com',
            'pais_id' => 1,
            'departamento_id' => 95,
            'municipio_id' => 824,
            'direccion' => 'CALLE FALSA 123',
            'status' => 1,
        ]);

        Persona::create([
            'id' => 3,
            'tipo_documento' => NULL,
            'numero_documento' => 123456789,
            'primer_nombre' => 'INSTRUCTOR',
            'segundo_nombre' => NULL,
            'primer_apellido' => 'PRUEBA',
            'segundo_apellido' => NULL,
            'fecha_nacimiento' => '2000-01-01',
            'genero' => NULL,
            'telefono' => NULL,
            'celular' => NULL,
            'email' => 'instructor@instructor.com',
            'pais_id' => 1,
            'departamento_id' => 95,
            'municipio_id' => 824,
            'direccion' => 'CALLE FALSA 123',
            'status' => 1,
        ]);
    }
}
