<?php

namespace Database\Seeders;

use App\Models\Ambiente;
use App\Models\Aprendiz;
use App\Models\AprendizFicha;
use App\Models\AspiranteComplementario;
use App\Models\ComplementarioOfertado;
use App\Models\FichaCaracterizacion;
use App\Models\FichaDiasFormacion;
use App\Models\Instructor;
use App\Models\InstructorFichaCaracterizacion;
use App\Models\InstructorFichaDias;
use App\Models\Inventario\Aprobacion;
use App\Models\Inventario\ContratoConvenio;
use App\Models\Inventario\DetalleOrden;
use App\Models\Inventario\Orden;
use App\Models\Inventario\Producto;
use App\Models\Inventario\Proveedor;
use App\Models\Parametro;
use App\Models\ProgramaFormacion;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\CompetenciaPrograma;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->truncateGeneratedData();
        $this->call([
            RolePermissionSeeder::class,
            ParametroSeeder::class,
            TemaSeeder::class,
            PaisSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            PersonaSeeder::class,
            UsersTableSeeder::class,
            RegionalSeeder::class,
            CentroFormacionSeeder::class,
            SedeSeeder::class,
            BloqueSeeder::class,
            PisoSeeder::class,
            AmbienteSeeder::class,
            RedConocimientoSeeder::class,
            JornadaFormacionSeeder::class,
            CompetenciaSeeder::class,
            ProductoSeeder::class, // Crear productos para agregar al módulo de inventario
            UsersTableSeeder::class,
            // Complementarios ofertados
            // ComplementariosOfertadosSeeder::class,
            // Aspirantes complementarios
            // AspirantesComplementariosSeeder::class,
            // Categorias de caracterizacion para complementarios
            // CategoriaCaracterizacionComplementariosSeeder::class,
        ]);

    }
    private function truncateGeneratedData(): void
    {
        $tables = [
            'instructor_ficha_dias',
            'instructor_fichas_caracterizacion',
            'ficha_dias_formacion',
            'fichas_caracterizacion',
            'aprendiz_fichas_caracterizacion',
            'aprendices',
            'proveedores',
            'contratos_convenios',
            'productos',
            'detalle_ordenes',
            'aprobaciones',
            'ordenes',
            'complementarios_ofertados_dias_formacion',
            'aspirantes_complementarios',
            'complementarios_ofertados',
        ];
        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();
    }
}

