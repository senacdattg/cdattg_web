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
                'numero_documento' => 987654321,
                'primer_nombre' => 'SUPER',
                'segundo_nombre' => null,
                'primer_apellido' => 'ADMINISTRADOR',
                'segundo_apellido' => null,
                'fecha_nacimiento' => '1980-01-01',
                'genero' => 9,
                'telefono' => null,
                'celular' => '3000000000',
                'email' => 'admin@admin.com',
                'pais_id' => 1,
                'departamento_id' => 95,
                'municipio_id' => 824,
                'direccion' => 'CALLE 10 #10-10',
                'status' => 1,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );

        Persona::updateOrCreate(
            ['id' => 2],
            [
                'tipo_documento' => 3,
                'numero_documento' => 654321123,
                'primer_nombre' => 'USUARIO',
                'segundo_nombre' => 'DEMO',
                'primer_apellido' => 'CDATTG',
                'segundo_apellido' => 'PRUEBAS',
                'fecha_nacimiento' => '1990-06-15',
                'genero' => 10,
                'telefono' => null,
                'celular' => '3010000000',
                'email' => 'demo@cdattg.local',
                'pais_id' => 1,
                'departamento_id' => 95,
                'municipio_id' => 824,
                'direccion' => 'CARRERA 8 #12-34',
                'status' => 1,
                'user_create_id' => null,
                'user_edit_id' => null,
            ]
        );
    }
}
