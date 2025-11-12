<?php

namespace Database\Seeders;

use App\Models\Persona;
use Illuminate\Database\Seeder;
use Database\Factories\PersonaFactory;
use Database\Seeders\Concerns\TruncatesTables;

class PersonaSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(Persona::class);

        
        Persona::updateOrCreate(
            ['id' => 1],
            [
                'tipo_documento' => 3,
                'numero_documento' => 111111111,
                'primer_nombre' => 'BOT',
                'segundo_nombre' => null,
                'primer_apellido' => 'AUTOMATICO',
                'segundo_apellido' => null,
                'fecha_nacimiento' => '2000-01-01',
                'genero' => 9,
                'telefono' => null,
                'celular' => '3001111111',
                'email' => 'bot@dataguaviare.com',
                'pais_id' => 1,
                'departamento_id' => 95,
                'municipio_id' => 1,
                'direccion' => 'CALLE 11 #11-11',
                'status' => 1,
                'user_create_id' => null,
                'user_edit_id' => null,
                ]
            );

            Persona::updateOrCreate(
                ['id' => 2],
                [
                    'tipo_documento' => 3,
                    'numero_documento' => 987654321,
                    'primer_nombre' => 'SUPER',
                    'segundo_nombre' => null,
                    'primer_apellido' => 'ADMINISTRADOR',
                    'segundo_apellido' => null,
                    'fecha_nacimiento' => '1980-01-01',
                    'genero' => 9,
                    'telefono' => null,
                    'celular' => '3000000000',
                    'email' => 'superadmin@dataguaviare.com',
                    'pais_id' => 1,
                    'departamento_id' => 95,
                    'municipio_id' => 1,
                    'direccion' => 'CALLE 10 #10-10',
                    'status' => 1,
                    'user_create_id' => null,
                    'user_edit_id' => null,
                ]
            );

        Persona::updateOrCreate(
            ['id' => 3],
            [
                'tipo_documento' => 3,
                'numero_documento' => 654321123,
                'primer_nombre' => 'ADMINISTRADOR',
                'segundo_nombre' => 'DEMO',
                'primer_apellido' => 'CDATTG',
                'segundo_apellido' => 'ADMIN',
                'fecha_nacimiento' => '1990-06-15',
                'genero' => 10,
                'telefono' => null,
                'celular' => '3010000000',
                'email' => 'admin@dataguaviare.com',
                'pais_id' => 1,
                'departamento_id' => 95,
                'municipio_id' => 1,
                'direccion' => 'CARRERA 8 #12-34',
                'status' => 1,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );

        Persona::updateOrCreate(
            ['id' => 4],
            [
                'tipo_documento' => 3,
                'numero_documento' => 555555555,
                'primer_nombre' => 'INSTRUCTOR',
                'segundo_nombre' => 'DEMO',
                'primer_apellido' => 'CDATTG',
                'segundo_apellido' => 'PRUEBAS',
                'fecha_nacimiento' => '1985-04-10',
                'genero' => 9,
                'telefono' => null,
                'celular' => '3025555555',
                'email' => 'instructor@dataguaviare.com',
                'pais_id' => 1,
                'departamento_id' => 95,
                'municipio_id' => 1,
                'direccion' => 'CALLE 12 #13-56',
                'status' => 1,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );

        Persona::updateOrCreate(
            ['id' => 5],
            [
                'tipo_documento' => 3,
                'numero_documento' => 444444444,
                'primer_nombre' => 'APRENDIZ',
                'segundo_nombre' => 'UNO',
                'primer_apellido' => 'CDATTG',
                'segundo_apellido' => 'PRUEBAS',
                'fecha_nacimiento' => '2002-03-20',
                'genero' => 10,
                'telefono' => null,
                'celular' => '3034444444',
                'email' => 'aprendiz1@dataguaviare.com',
                'pais_id' => 1,
                'departamento_id' => 95,
                'municipio_id' => 1,
                'direccion' => 'AVENIDA 5 #22-10',
                'status' => 1,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );

        Persona::updateOrCreate(
            ['id' => 6],
            [
                'tipo_documento' => 3,
                'numero_documento' => 333333333,
                'primer_nombre' => 'APRENDIZ',
                'segundo_nombre' => 'DOS',
                'primer_apellido' => 'CDATTG',
                'segundo_apellido' => 'PRUEBAS',
                'fecha_nacimiento' => '2003-07-05',
                'genero' => 9,
                'telefono' => null,
                'celular' => '3043333333',
                'email' => 'aprendiz2@dataguaviare.com',
                'pais_id' => 1,
                'departamento_id' => 95,
                'municipio_id' => 1,
                'direccion' => 'AVENIDA 6 #18-20',
                'status' => 1,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );
    }
}
