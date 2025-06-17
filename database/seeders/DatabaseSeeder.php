<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Primero los roles y permisos
            RolePermissionSeeder::class,

            // Luego los datos geográficos
            PaisSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,

            // Crear usuarios sin persona_id (super-admin, admin e instructor)
            PersonaSeeder::class,
            UsersTableSeeder::class,

            // Crear infraestructura que dependen del usuario super admin
            RegionalSeeder::class,
            CentroFormacionSeeder::class,
            SedeSeeder::class,
            BloqueSeeder::class,
            PisoSeeder::class,
            AmbienteSeeder::class,

            //Crear tema-parametro que depende del usuario super admin
            ParametroSeeder::class,
            TemaSeeder::class,

            //Crear estructura academica que depende del usuario super admin
            RedConocimientoSeeder::class,
            ProgramasFormacionSeeder::class,
            JornadaFormacionSeeder::class,

            // Crear personas que dependen del usuario super admin
            UpdatePersonaSeeder::class,
            PersonasAprendicesSeeder::class,

            // Finalmente las relaciones
            InstructorSeeder::class,
            UpdatePersonasUserFieldsSeeder::class,
            AprendizSeeder::class,
            FichaCaracterizacionSeeder::class,//Depende super-admin, instructor, ambiente, modalidad de formacion, sede, jornada
            FichaDiasFormacionSeeder::class,//Depende de ficha, dia(tema-parametro), super admin
            InstructorFichaCaracterizacionSeeder::class,//Depende de una ficha y un instructor
            InstructorFichaDiasSeeder::class,//Depende de una ficha y tema-parametro
            AprendizFichaSeeder::class,//Depende de aprendices y ficha
        ]);
    }
}
