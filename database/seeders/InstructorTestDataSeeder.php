<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Persona;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Regional;
use App\Models\RedConocimiento;
use App\Models\Tema;
use Carbon\Carbon;

class InstructorTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creando instructores adicionales para pruebas...');

        // Solo ejecutar si no hay instructores existentes
        if (Instructor::count() > 0) {
            $this->command->info('Ya existen instructores en la base de datos. Saltando creación de datos de prueba.');
            return;
        }

        DB::beginTransaction();
        
        try {
            // Crear instructores con diferentes escenarios para pruebas
            $this->createInstructorsWithDifferentScenarios();
            
            DB::commit();
            
            $this->command->info('Instructores de prueba creados exitosamente.');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Error creando instructores de prueba: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Crear instructores con diferentes escenarios para pruebas
     */
    private function createInstructorsWithDifferentScenarios(): void
    {
        $regionalGuaviare = Regional::first();
        $tipoDocumento = Tema::whereHas('parametros', function($q) {
            $q->where('name', 'Cédula de Ciudadanía');
        })->first()?->parametros()->where('name', 'Cédula de Ciudadanía')->first();

        $scenarios = [
            // Instructor con máxima carga de fichas (5 fichas)
            [
                'persona' => [
                    'primer_nombre' => 'Juan',
                    'segundo_nombre' => 'Carlos',
                    'primer_apellido' => 'Máximo',
                    'segundo_apellido' => 'Carga',
                    'numero_documento' => '11111111',
                    'email' => 'juan.maximo@sena.edu.co',
                    'telefono' => '3001111111',
                    'direccion' => 'Dirección Máxima Carga, San José del Guaviare',
                    'fecha_nacimiento' => '1980-01-01',
                    'genero' => 'M',
                    'tipo_documento_id' => $tipoDocumento?->id ?? 1
                ],
                'instructor' => [
                    'regional_id' => $regionalGuaviare?->id ?? 1,
                    'status' => true,
                    'especialidades' => [
                        'principal' => 'Tecnologías de la Información y las Comunicaciones',
                        'secundarias' => ['Electrónica', 'Mecánica Industrial']
                    ],
                    'competencias' => [
                        'Programación Avanzada',
                        'Bases de Datos',
                        'Redes',
                        'Sistemas Operativos'
                    ],
                    'anos_experiencia' => 15,
                    'experiencia_laboral' => 'Instructor con máxima experiencia para pruebas de límite de fichas.'
                ]
            ],
            // Instructor sin especialidades (para pruebas de validación)
            [
                'persona' => [
                    'primer_nombre' => 'María',
                    'segundo_nombre' => 'Sin',
                    'primer_apellido' => 'Especialidades',
                    'segundo_apellido' => 'Test',
                    'numero_documento' => '22222222',
                    'email' => 'maria.sin@specialidades.sena.edu.co',
                    'telefono' => '3002222222',
                    'direccion' => 'Dirección Sin Especialidades, San José del Guaviare',
                    'fecha_nacimiento' => '1990-01-01',
                    'genero' => 'F',
                    'tipo_documento_id' => $tipoDocumento?->id ?? 1
                ],
                'instructor' => [
                    'regional_id' => $regionalGuaviare?->id ?? 1,
                    'status' => true,
                    'especialidades' => [
                        'principal' => null,
                        'secundarias' => []
                    ],
                    'competencias' => [],
                    'anos_experiencia' => 1,
                    'experiencia_laboral' => 'Instructor nuevo sin especialidades asignadas.'
                ]
            ],
            // Instructor inactivo
            [
                'persona' => [
                    'primer_nombre' => 'Pedro',
                    'segundo_nombre' => 'Inactivo',
                    'primer_apellido' => 'Test',
                    'segundo_apellido' => 'User',
                    'numero_documento' => '33333333',
                    'email' => 'pedro.inactivo@sena.edu.co',
                    'telefono' => '3003333333',
                    'direccion' => 'Dirección Inactivo, San José del Guaviare',
                    'fecha_nacimiento' => '1985-01-01',
                    'genero' => 'M',
                    'tipo_documento_id' => $tipoDocumento?->id ?? 1
                ],
                'instructor' => [
                    'regional_id' => $regionalGuaviare?->id ?? 1,
                    'status' => false,
                    'especialidades' => [
                        'principal' => 'Mecánica Industrial',
                        'secundarias' => ['Construcción']
                    ],
                    'competencias' => [
                        'Soldadura',
                        'Mecanizado'
                    ],
                    'anos_experiencia' => 8,
                    'experiencia_laboral' => 'Instructor inactivo para pruebas.'
                ]
            ],
            // Instructor con poca experiencia
            [
                'persona' => [
                    'primer_nombre' => 'Ana',
                    'segundo_nombre' => 'Poca',
                    'primer_apellido' => 'Experiencia',
                    'segundo_apellido' => 'Test',
                    'numero_documento' => '44444444',
                    'email' => 'ana.poca@sena.edu.co',
                    'telefono' => '3004444444',
                    'direccion' => 'Dirección Poca Experiencia, San José del Guaviare',
                    'fecha_nacimiento' => '1995-01-01',
                    'genero' => 'F',
                    'tipo_documento_id' => $tipoDocumento?->id ?? 1
                ],
                'instructor' => [
                    'regional_id' => $regionalGuaviare?->id ?? 1,
                    'status' => true,
                    'especialidades' => [
                        'principal' => 'Gastronomía',
                        'secundarias' => []
                    ],
                    'competencias' => [
                        'Cocina Básica'
                    ],
                    'anos_experiencia' => 1,
                    'experiencia_laboral' => 'Instructor con experiencia mínima para pruebas.'
                ]
            ],
            // Instructor con múltiples especialidades
            [
                'persona' => [
                    'primer_nombre' => 'Carlos',
                    'segundo_nombre' => 'Múltiples',
                    'primer_apellido' => 'Especialidades',
                    'segundo_apellido' => 'Test',
                    'numero_documento' => '55555555',
                    'email' => 'carlos.multi@sena.edu.co',
                    'telefono' => '3005555555',
                    'direccion' => 'Dirección Múltiples Especialidades, San José del Guaviare',
                    'fecha_nacimiento' => '1975-01-01',
                    'genero' => 'M',
                    'tipo_documento_id' => $tipoDocumento?->id ?? 1
                ],
                'instructor' => [
                    'regional_id' => $regionalGuaviare?->id ?? 1,
                    'status' => true,
                    'especialidades' => [
                        'principal' => 'Electrónica',
                        'secundarias' => [
                            'Tecnologías de la Información y las Comunicaciones',
                            'Mecánica Industrial',
                            'Construcción'
                        ]
                    ],
                    'competencias' => [
                        'Electrónica Digital',
                        'Programación',
                        'Mecánica',
                        'Construcción Civil'
                    ],
                    'anos_experiencia' => 20,
                    'experiencia_laboral' => 'Instructor multidisciplinario con amplia experiencia.'
                ]
            ]
        ];

        foreach ($scenarios as $data) {
            $this->createInstructor($data);
        }
    }

    /**
     * Crear un instructor completo con persona y usuario
     */
    private function createInstructor(array $data): void
    {
        // Crear persona
        $persona = Persona::create($data['persona']);
        
        // Crear usuario
        $usuario = User::create([
            'name' => $data['persona']['primer_nombre'] . ' ' . $data['persona']['primer_apellido'],
            'email' => $data['persona']['email'],
            'password' => Hash::make('12345678'), // Contraseña por defecto
            'persona_id' => $persona->id,
            'status' => $data['instructor']['status'] ? 1 : 0,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Crear instructor
        $instructorData = $data['instructor'];
        $instructorData['persona_id'] = $persona->id;
        $instructorData['user_create_id'] = 1; // Usuario administrador por defecto
        $instructorData['created_at'] = now();
        $instructorData['updated_at'] = now();

        $instructor = Instructor::create($instructorData);

        // Actualizar campos cache
        $instructor->update([
            'numero_documento_cache' => $persona->numero_documento,
            'nombre_completo_cache' => $persona->primer_nombre . ' ' . 
                                    ($persona->segundo_nombre ? $persona->segundo_nombre . ' ' : '') .
                                    $persona->primer_apellido . ' ' . 
                                    ($persona->segundo_apellido ? $persona->segundo_apellido : '')
        ]);

        $this->command->info("Instructor de prueba creado: {$instructor->nombre_completo_cache} - {$persona->email}");
    }
}
