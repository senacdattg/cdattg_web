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

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::beginTransaction();
        
        try {
            // Obtener regionales y redes de conocimiento existentes
            $regionalGuaviare = Regional::where('nombre', 'like', '%Guaviare%')->first();
            $regionalMeta = Regional::where('nombre', 'like', '%Meta%')->first();
            
            $redInformatica = RedConocimiento::where('nombre', 'like', '%Informática%')->first();
            $redElectronica = RedConocimiento::where('nombre', 'like', '%Electrónica%')->first();
            $redMecanica = RedConocimiento::where('nombre', 'like', '%Mecánica%')->first();
            $redConstruccion = RedConocimiento::where('nombre', 'like', '%Construcción%')->first();
            $redGastronomia = RedConocimiento::where('nombre', 'like', '%Gastronomía%')->first();
            
            // Obtener tipo de documento C.C.
            $tipoDocumento = Tema::whereHas('parametros', function($q) {
                $q->where('name', 'Cédula de Ciudadanía');
            })->first()?->parametros()->where('name', 'Cédula de Ciudadanía')->first();

            // Instructores de ejemplo
            $instructores = [
                [
                    'persona' => [
                        'primer_nombre' => 'Carlos',
                        'segundo_nombre' => 'Alberto',
                        'primer_apellido' => 'Rodriguez',
                        'segundo_apellido' => 'García',
                        'numero_documento' => '12345678',
                        'email' => 'carlos.rodriguez@sena.edu.co',
                        'telefono' => '3001234567',
                        'direccion' => 'Calle 15 #10-25, San José del Guaviare',
                        'fecha_nacimiento' => '1985-03-15',
                        'genero' => 'M',
                        'tipo_documento_id' => $tipoDocumento?->id ?? 1
                    ],
                    'instructor' => [
                        'regional_id' => $regionalGuaviare?->id ?? 1,
                        'status' => true,
                        'especialidades' => [
                            'principal' => $redInformatica?->nombre ?? 'Tecnologías de la Información y las Comunicaciones',
                            'secundarias' => [
                                $redElectronica?->nombre ?? 'Sistemas de Información'
                            ]
                        ],
                        'competencias' => [
                            'Programación Web',
                            'Bases de Datos',
                            'Redes de Computadores',
                            'Desarrollo de Software'
                        ],
                        'anos_experiencia' => 8,
                        'experiencia_laboral' => 'Más de 8 años de experiencia en desarrollo de software, programación web y administración de sistemas. Especialista en tecnologías como PHP, Laravel, JavaScript, HTML5, CSS3 y bases de datos MySQL y PostgreSQL.'
                    ]
                ],
                [
                    'persona' => [
                        'primer_nombre' => 'María',
                        'segundo_nombre' => 'Elena',
                        'primer_apellido' => 'Martínez',
                        'segundo_apellido' => 'López',
                        'numero_documento' => '87654321',
                        'email' => 'maria.martinez@sena.edu.co',
                        'telefono' => '3002345678',
                        'direccion' => 'Carrera 20 #15-40, San José del Guaviare',
                        'fecha_nacimiento' => '1982-07-22',
                        'genero' => 'F',
                        'tipo_documento_id' => $tipoDocumento?->id ?? 1
                    ],
                    'instructor' => [
                        'regional_id' => $regionalGuaviare?->id ?? 1,
                        'status' => true,
                        'especialidades' => [
                            'principal' => $redElectronica?->nombre ?? 'Electrónica',
                            'secundarias' => [
                                $redInformatica?->nombre ?? 'Tecnologías de la Información y las Comunicaciones',
                                $redMecanica?->nombre ?? 'Mecánica Industrial'
                            ]
                        ],
                        'competencias' => [
                            'Electrónica Digital',
                            'Microcontroladores',
                            'Sistemas Embebidos',
                            'Automatización Industrial',
                            'Mantenimiento Electrónico'
                        ],
                        'anos_experiencia' => 12,
                        'experiencia_laboral' => 'Ingeniera electrónica con más de 12 años de experiencia en automatización industrial, diseño de circuitos electrónicos y sistemas embebidos. Especialista en microcontroladores PIC y Arduino, PLC y sistemas SCADA.'
                    ]
                ],
                [
                    'persona' => [
                        'primer_nombre' => 'Jorge',
                        'segundo_nombre' => 'Luis',
                        'primer_apellido' => 'Hernández',
                        'segundo_apellido' => 'Vargas',
                        'numero_documento' => '11223344',
                        'email' => 'jorge.hernandez@sena.edu.co',
                        'telefono' => '3003456789',
                        'direccion' => 'Calle 25 #30-15, San José del Guaviare',
                        'fecha_nacimiento' => '1978-11-08',
                        'genero' => 'M',
                        'tipo_documento_id' => $tipoDocumento?->id ?? 1
                    ],
                    'instructor' => [
                        'regional_id' => $regionalGuaviare?->id ?? 1,
                        'status' => true,
                        'especialidades' => [
                            'principal' => $redMecanica?->nombre ?? 'Mecánica Industrial',
                            'secundarias' => [
                                $redConstruccion?->nombre ?? 'Construcción'
                            ]
                        ],
                        'competencias' => [
                            'Mantenimiento Industrial',
                            'Soldadura',
                            'Mecanizado',
                            'Dibujo Técnico',
                            'Mantenimiento de Maquinaria'
                        ],
                        'anos_experiencia' => 15,
                        'experiencia_laboral' => 'Técnico mecánico con más de 15 años de experiencia en mantenimiento industrial, soldadura y mecanizado. Especialista en máquinas herramientas, sistemas hidráulicos y neumáticos, y mantenimiento preventivo y correctivo.'
                    ]
                ],
                [
                    'persona' => [
                        'primer_nombre' => 'Ana',
                        'segundo_nombre' => 'María',
                        'primer_apellido' => 'Silva',
                        'segundo_apellido' => 'Ramírez',
                        'numero_documento' => '55667788',
                        'email' => 'ana.silva@sena.edu.co',
                        'telefono' => '3004567890',
                        'direccion' => 'Carrera 10 #25-30, San José del Guaviare',
                        'fecha_nacimiento' => '1990-05-12',
                        'genero' => 'F',
                        'tipo_documento_id' => $tipoDocumento?->id ?? 1
                    ],
                    'instructor' => [
                        'regional_id' => $regionalGuaviare?->id ?? 1,
                        'status' => true,
                        'especialidades' => [
                            'principal' => $redGastronomia?->nombre ?? 'Gastronomía',
                            'secundarias' => []
                        ],
                        'competencias' => [
                            'Cocina Internacional',
                            'Pastelería',
                            'Panadería',
                            'Gestión de Cocina',
                            'Nutrición'
                        ],
                        'anos_experiencia' => 6,
                        'experiencia_laboral' => 'Chef profesional con más de 6 años de experiencia en cocina internacional, pastelería y panadería. Especialista en técnicas culinarias avanzadas, gestión de cocina y nutrición aplicada a la gastronomía.'
                    ]
                ],
                [
                    'persona' => [
                        'primer_nombre' => 'Roberto',
                        'segundo_nombre' => 'Carlos',
                        'primer_apellido' => 'Morales',
                        'segundo_apellido' => 'Jiménez',
                        'numero_documento' => '99887766',
                        'email' => 'roberto.morales@sena.edu.co',
                        'telefono' => '3005678901',
                        'direccion' => 'Calle 30 #40-20, San José del Guaviare',
                        'fecha_nacimiento' => '1983-09-18',
                        'genero' => 'M',
                        'tipo_documento_id' => $tipoDocumento?->id ?? 1
                    ],
                    'instructor' => [
                        'regional_id' => $regionalGuaviare?->id ?? 1,
                        'status' => true,
                        'especialidades' => [
                            'principal' => $redConstruccion?->nombre ?? 'Construcción',
                            'secundarias' => [
                                $redMecanica?->nombre ?? 'Mecánica Industrial'
                            ]
                        ],
                        'competencias' => [
                            'Construcción Civil',
                            'Dibujo Arquitectónico',
                            'Topografía',
                            'Obras Civiles',
                            'Gestión de Proyectos'
                        ],
                        'anos_experiencia' => 10,
                        'experiencia_laboral' => 'Ingeniero civil con más de 10 años de experiencia en construcción civil, diseño arquitectónico y gestión de proyectos. Especialista en obras civiles, topografía y supervisión de construcción.'
                    ]
                ],
                [
                    'persona' => [
                        'primer_nombre' => 'Laura',
                        'segundo_nombre' => 'Patricia',
                        'primer_apellido' => 'Gómez',
                        'segundo_apellido' => 'Torres',
                        'numero_documento' => '44556677',
                        'email' => 'laura.gomez@sena.edu.co',
                        'telefono' => '3006789012',
                        'direccion' => 'Carrera 5 #12-35, San José del Guaviare',
                        'fecha_nacimiento' => '1987-12-03',
                        'genero' => 'F',
                        'tipo_documento_id' => $tipoDocumento?->id ?? 1
                    ],
                    'instructor' => [
                        'regional_id' => $regionalGuaviare?->id ?? 1,
                        'status' => false, // Instructor inactivo para pruebas
                        'especialidades' => [
                            'principal' => $redInformatica?->nombre ?? 'Tecnologías de la Información y las Comunicaciones',
                            'secundarias' => []
                        ],
                        'competencias' => [
                            'Diseño Gráfico',
                            'Multimedia',
                            'Animación',
                            'Edición de Video',
                            'Marketing Digital'
                        ],
                        'anos_experiencia' => 5,
                        'experiencia_laboral' => 'Diseñadora gráfica con más de 5 años de experiencia en diseño digital, multimedia y marketing digital. Especialista en Adobe Creative Suite, animación 2D y 3D, y estrategias de marketing digital.'
                    ]
                ]
            ];

            // Crear instructores
            foreach ($instructores as $data) {
                $this->createInstructor($data);
            }

            DB::commit();
            
            $this->command->info('Instructores de ejemplo creados exitosamente.');
            
        } catch (\Exception $e) {
            DB::rollback();
            $this->command->error('Error creando instructores: ' . $e->getMessage());
            throw $e;
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

        // Actualizar cache de campos calculados para el instructor
        $instructorData = $data['instructor'];
        $instructorData['persona_id'] = $persona->id;
        $instructorData['user_create_id'] = 1; // Usuario administrador por defecto
        $instructorData['created_at'] = now();
        $instructorData['updated_at'] = now();

        // Crear instructor
        $instructor = Instructor::create($instructorData);

        // Actualizar campos cache después de crear el instructor
        $instructor->update([
            'numero_documento_cache' => $persona->numero_documento,
            'nombre_completo_cache' => $persona->primer_nombre . ' ' . 
                                    ($persona->segundo_nombre ? $persona->segundo_nombre . ' ' : '') .
                                    $persona->primer_apellido . ' ' . 
                                    ($persona->segundo_apellido ? $persona->segundo_apellido : '')
        ]);

        $this->command->info("Instructor creado: {$instructor->nombre_completo_cache} - {$persona->email}");
    }

    /**
     * Crear instructores adicionales para pruebas de carga
     */
    public function createAdditionalInstructors(int $count = 10): void
    {
        $regionalGuaviare = Regional::where('nombre', 'like', '%Guaviare%')->first();
        $tipoDocumento = Tema::whereHas('parametros', function($q) {
            $q->where('name', 'Cédula de Ciudadanía');
        })->first()?->parametros()->where('name', 'Cédula de Ciudadanía')->first();

        $nombres = ['Juan', 'Pedro', 'Luis', 'Miguel', 'Andrés', 'Fernando', 'Ricardo', 'Diego', 'Alejandro', 'Gabriel'];
        $apellidos = ['García', 'López', 'Martínez', 'González', 'Pérez', 'Sánchez', 'Ramírez', 'Cruz', 'Flores', 'Herrera'];
        $especialidades = [
            'Tecnologías de la Información y las Comunicaciones',
            'Electrónica',
            'Mecánica Industrial',
            'Construcción',
            'Gastronomía'
        ];

        for ($i = 1; $i <= $count; $i++) {
            $primerNombre = $nombres[array_rand($nombres)];
            $primerApellido = $apellidos[array_rand($apellidos)];
            $segundoApellido = $apellidos[array_rand($apellidos)];
            $numeroDocumento = str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
            $email = strtolower($primerNombre . '.' . $primerApellido . $i . '@sena.edu.co');

            $data = [
                'persona' => [
                    'primer_nombre' => $primerNombre,
                    'segundo_nombre' => rand(0, 1) ? $nombres[array_rand($nombres)] : null,
                    'primer_apellido' => $primerApellido,
                    'segundo_apellido' => $segundoApellido,
                    'numero_documento' => $numeroDocumento,
                    'email' => $email,
                    'telefono' => '300' . str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT),
                    'direccion' => 'Dirección ' . $i . ', San José del Guaviare',
                    'fecha_nacimiento' => Carbon::now()->subYears(rand(25, 55))->format('Y-m-d'),
                    'genero' => rand(0, 1) ? 'M' : 'F',
                    'tipo_documento_id' => $tipoDocumento?->id ?? 1
                ],
                'instructor' => [
                    'regional_id' => $regionalGuaviare?->id ?? 1,
                    'status' => rand(0, 1),
                    'especialidades' => [
                        'principal' => $especialidades[array_rand($especialidades)],
                        'secundarias' => rand(0, 1) ? [$especialidades[array_rand($especialidades)]] : []
                    ],
                    'competencias' => array_slice([
                        'Competencia 1', 'Competencia 2', 'Competencia 3', 'Competencia 4', 'Competencia 5'
                    ], 0, rand(2, 4)),
                    'anos_experiencia' => rand(1, 20),
                    'experiencia_laboral' => 'Experiencia laboral del instructor ' . $i . '.'
                ]
            ];

            $this->createInstructor($data);
        }
    }
}