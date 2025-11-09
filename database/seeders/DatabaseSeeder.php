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
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
            UpdatePersonaSeeder::class,
            SuperAdminSeeder::class,
            UsersTableSeeder::class,
            RegionalSeeder::class,
            CentroFormacionSeeder::class,
            SedeSeeder::class,
            BloqueSeeder::class,
            PisoSeeder::class,
            AmbienteSeeder::class,
            RedConocimientoSeeder::class,
            ProgramasFormacionSeeder::class,
            JornadaFormacionSeeder::class,
            UpdatePersonasUserFieldsSeeder::class,
            CompetenciaSeeder::class,
            ResultadosAprendizajeSeeder::class,
            ResultadosCompetenciasSeeder::class,
            GuiasAprendizajeSeeder::class,
            ResultadosGuiasSeeder::class,
            CompetenciaProgramaSeeder::class,
            ProductoSeeder::class, // Crear productos para agregar al módulo de inventario
            // Complementarios ofertados
            // ComplementariosOfertadosSeeder::class,
            // Aspirantes complementarios
            // AspirantesComplementariosSeeder::class,
            // Categorias de caracterizacion para complementarios
            // CategoriaCaracterizacionComplementariosSeeder::class,
        ]);
        $adminUser = User::where('email', 'admin@admin.com')->first() ?? User::first();
        $adminUserId = $adminUser?->id ?? 1;
        // Instructores de referencia
        $instructors = Instructor::factory()
            ->count(8)
            ->state(fn () => [
                'user_create_id' => $adminUserId,
                'user_edit_id' => $adminUserId,
            ])
            ->create();
        // Fichas de caracterización ligadas a instructores disponibles
        $fichas = new Collection();
        foreach ($instructors->shuffle()->take(4) as $instructor) {
            $fichas->push(
                FichaCaracterizacion::factory()->create([
                    'instructor_id' => $instructor->id,
                    'user_create_id' => $adminUserId,
                    'user_edit_id' => $adminUserId,
                ])
            );
        }
        $fichas->each(function (FichaCaracterizacion $ficha) use ($adminUserId) {
            FichaDiasFormacion::factory()
                ->count(3)
                ->for($ficha, 'ficha')
                ->create();
            $asignacion = InstructorFichaCaracterizacion::factory()->create([
                'instructor_id' => $ficha->instructor_id,
                'ficha_id' => $ficha->id,
                'fecha_inicio' => $ficha->fecha_inicio,
                'fecha_fin' => $ficha->fecha_fin,
            ]);
            InstructorFichaDias::factory()
                ->count(2)
                ->for($asignacion, 'instructorFicha')
                ->create();
        });
        // Aprendices y asignación a fichas
        $aprendices = Aprendiz::factory()
            ->count(25)
            ->state(fn () => [
                'user_create_id' => $adminUserId,
                'user_edit_id' => $adminUserId,
            ])
            ->create();
        $aprendices->each(function (Aprendiz $aprendiz) use ($fichas) {
            if ($fichas->isEmpty()) {
                return;
            }
            AprendizFicha::factory()->create([
                'aprendiz_id' => $aprendiz->id,
                'ficha_id' => $fichas->random()->id,
            ]);
        });
        // Inventario base utilizando factories
        $proveedores = Proveedor::factory()
            ->count(5)
            ->state(fn () => [
                'user_create_id' => $adminUserId,
                'user_update_id' => $adminUserId,
            ])
            ->create();
        $contratos = ContratoConvenio::factory()
            ->count(5)
            ->state(function () use ($proveedores, $adminUserId) {
                return [
                    'proveedor_id' => $proveedores->random()->id,
                    'user_create_id' => $adminUserId,
                    'user_update_id' => $adminUserId,
                ];
            })
            ->create();
        $ambienteIds = Ambiente::query()->pluck('id');
        $productos = Producto::factory()
            ->count(12)
            ->state(function () use ($proveedores, $contratos, $ambienteIds, $adminUserId) {
                return [
                    'proveedor_id' => $proveedores->random()->id,
                    'contrato_convenio_id' => $contratos->random()->id,
                    'ambiente_id' => $ambienteIds->random(),
                    'user_create_id' => $adminUserId,
                    'user_update_id' => $adminUserId,
                ];
            })
            ->create();
        $ordenes = Orden::factory()
            ->count(6)
            ->state(fn () => [
                'user_create_id' => $adminUserId,
                'user_update_id' => $adminUserId,
            ])
            ->create();
        $detalles = new Collection();
        foreach ($ordenes as $orden) {
            $nuevosDetalles = DetalleOrden::factory()
                ->count(fake()->numberBetween(1, 3))
                ->state(function () use ($orden, $productos, $adminUserId) {
                    return [
                        'orden_id' => $orden->id,
                        'producto_id' => $productos->random()->id,
                        'user_create_id' => $adminUserId,
                        'user_update_id' => $adminUserId,
                    ];
                })
                ->create();
            $detalles = $detalles->merge($nuevosDetalles);
        }
        $detalles->unique('id')
            ->take(max(1, (int)floor($detalles->count() * 0.6)))
            ->each(function (DetalleOrden $detalle) use ($adminUserId) {
                Aprobacion::factory()->create([
                    'detalle_orden_id' => $detalle->id,
                    'user_create_id' => $adminUserId,
                    'user_update_id' => $adminUserId,
                ]);
            });
        // Oferta complementaria y aspirantes
        $complementarios = ComplementarioOfertado::factory()->count(3)->create();
        $pairs = collect();
        foreach ($aprendices as $aprendiz) {
            foreach ($complementarios as $complementario) {
                $pairs->push([
                    'persona_id' => $aprendiz->persona_id,
                    'complementario_id' => $complementario->id,
                ]);
            }
        }
        $pairs
            ->shuffle()
            ->take(min(12, $pairs->count()))
            ->each(function (array $pair) {
                AspiranteComplementario::factory()
                    ->state($pair)
                    ->create();
            });
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
            DB::table($table)->truncate();
        }
        Schema::enableForeignKeyConstraints();
    }
}

