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
            // Primero los roles y permisos generales
            RolePermissionSeeder::class,
            
            // Permisos específicos de FichaCaracterizacion
            FichaCaracterizacionPermissionsSeeder::class,
            
            // Permisos específicos de Guías de Aprendizaje
            GuiasAprendizajePermissionsSeeder::class,
            
            // Permisos específicos de Resultados de Aprendizaje
            ResultadosAprendizajePermissionsSeeder::class,

            // Luego los datos geográficos
            PaisSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,

            // Crear personas primero (necesarias para usuarios)
            PersonaSeeder::class,
            
            // Crear Super Admin con todos los permisos
            SuperAdminSeeder::class,
            
            // Crear otros usuarios
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

            // las relaciones
            InstructorSeeder::class,
            InstructorTestDataSeeder::class,
            UpdatePersonasUserFieldsSeeder::class,
            AprendizSeeder::class,
            FichaCaracterizacionSeeder::class,//Depende super-admin, instructor, ambiente, modalidad de formacion, sede, jornada
            FichaDiasFormacionSeeder::class,//Depende de ficha, dia(tema-parametro), super admin
            InstructorFichaCaracterizacionSeeder::class,//Depende de una ficha y un instructor
            InstructorFichaDiasSeeder::class,//Depende de una ficha y tema-parametro
            AprendizFichaSeeder::class,//Depende de aprendices y ficha

            // Modelo de aprendizaje
            CompetenciaSeeder::class,
            ResultadosAprendizajeSeeder::class, 
            ResultadosCompetenciasSeeder::class,
            GuiasAprendizajeSeeder::class,
            ResultadosGuiasSeeder::class,
            CompetenciaProgramaSeeder::class,

            //Inventario:
            OrdenSeeder::class, //Crear ordenes
            ProveedoresSeeder::class, //Crear proveedores
            ContratosConveniosSeeder::class, //Crear contratos y convenios
            ProductoSeeder::class, // Crear productos para agregar al módulo de inventario
            DetalleOrdenSeeder::class, // Crear detalles de ordenes
            AprobacionSeeder::class, // Crear aprobaciones de ordenes
            // Complementarios ofertados
            ComplementariosOfertadosSeeder::class,
        ]);
    }
}
