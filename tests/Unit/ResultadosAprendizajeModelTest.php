<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResultadosAprendizaje;
use App\Models\Competencia;
use App\Models\GuiasAprendizaje;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResultadosAprendizajeModelTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $rap;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $this->rap = ResultadosAprendizaje::create([
            'codigo' => 'RAP001',
            'nombre' => 'Resultado de Prueba',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);
    }

    /**
     * Test: El modelo tiene los campos fillable correctos
     */
    public function test_modelo_tiene_campos_fillable()
    {
        $fillable = [
            'codigo',
            'nombre',
            'duracion',
            'fecha_inicio',
            'fecha_fin',
            'status',
            'user_create_id',
            'user_edit_id',
        ];

        $this->assertEquals($fillable, $this->rap->getFillable());
    }

    /**
     * Test: El modelo castea los campos correctamente
     */
    public function test_modelo_castea_campos_correctamente()
    {
        $casts = $this->rap->getCasts();

        $this->assertArrayHasKey('duracion', $casts);
        $this->assertArrayHasKey('status', $casts);
        $this->assertArrayHasKey('fecha_inicio', $casts);
        $this->assertArrayHasKey('fecha_fin', $casts);
    }

    /**
     * Test: Scope activos funciona correctamente
     */
    public function test_scope_activos_funciona()
    {
        // Crear RAP inactivo
        ResultadosAprendizaje::create([
            'codigo' => 'RAP002',
            'nombre' => 'RAP Inactivo',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 0,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $activos = ResultadosAprendizaje::activos()->get();

        $this->assertCount(1, $activos);
        $this->assertEquals('RAP001', $activos->first()->codigo);
    }

    /**
     * Test: Scope inactivos funciona correctamente
     */
    public function test_scope_inactivos_funciona()
    {
        ResultadosAprendizaje::create([
            'codigo' => 'RAP003',
            'nombre' => 'RAP Inactivo',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 0,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $inactivos = ResultadosAprendizaje::inactivos()->get();

        $this->assertCount(1, $inactivos);
        $this->assertEquals('RAP003', $inactivos->first()->codigo);
    }

    /**
     * Test: Helper isActivo funciona correctamente
     */
    public function test_helper_is_activo_funciona()
    {
        $this->assertTrue($this->rap->isActivo());

        $rapInactivo = ResultadosAprendizaje::create([
            'codigo' => 'RAP004',
            'nombre' => 'RAP Inactivo',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 0,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $this->assertFalse($rapInactivo->isActivo());
    }

    /**
     * Test: Helper duracionEnHoras funciona correctamente
     */
    public function test_helper_duracion_en_horas_funciona()
    {
        $this->assertEquals('40 horas', $this->rap->duracionEnHoras());
    }

    /**
     * Test: Helper tieneFechasDefinidas funciona correctamente
     */
    public function test_helper_tiene_fechas_definidas_funciona()
    {
        $this->assertTrue($this->rap->tieneFechasDefinidas());

        $rapSinFechas = ResultadosAprendizaje::create([
            'codigo' => 'RAP005',
            'nombre' => 'RAP Sin Fechas',
            'duracion' => 40,
            'fecha_inicio' => null,
            'fecha_fin' => null,
            'status' => 1,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $this->assertFalse($rapSinFechas->tieneFechasDefinidas());
    }

    /**
     * Test: Helper estaVigente funciona correctamente
     */
    public function test_helper_esta_vigente_funciona()
    {
        $rapVigente = ResultadosAprendizaje::create([
            'codigo' => 'RAP006',
            'nombre' => 'RAP Vigente',
            'duracion' => 40,
            'fecha_inicio' => now()->subDays(10)->format('Y-m-d'),
            'fecha_fin' => now()->addDays(10)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $this->assertTrue($rapVigente->estaVigente());

        $rapNoVigente = ResultadosAprendizaje::create([
            'codigo' => 'RAP007',
            'nombre' => 'RAP No Vigente',
            'duracion' => 40,
            'fecha_inicio' => now()->addDays(10)->format('Y-m-d'),
            'fecha_fin' => now()->addDays(20)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $this->assertFalse($rapNoVigente->estaVigente());
    }

    /**
     * Test: Relación con competencias funciona
     */
    public function test_relacion_con_competencias_funciona()
    {
        $competencia = Competencia::create([
            'codigo' => 'COMP001',
            'nombre' => 'Competencia de Prueba',
            'descripcion' => 'Descripción',
            'duracion' => 100,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(6)->format('Y-m-d'),
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $this->rap->competencias()->attach($competencia->id, [
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $this->rap->competencias);
        $this->assertCount(1, $this->rap->competencias);
        $this->assertEquals('COMP001', $this->rap->competencias->first()->codigo);
    }

    /**
     * Test: Relación con usuario creador funciona
     */
    public function test_relacion_con_usuario_creador_funciona()
    {
        $this->assertInstanceOf(User::class, $this->rap->userCreate);
        $this->assertEquals($this->user->id, $this->rap->userCreate->id);
    }

    /**
     * Test: Relación con usuario editor funciona
     */
    public function test_relacion_con_usuario_editor_funciona()
    {
        $this->assertInstanceOf(User::class, $this->rap->userEdit);
        $this->assertEquals($this->user->id, $this->rap->userEdit->id);
    }

    /**
     * Test: Scope porCodigo funciona correctamente
     */
    public function test_scope_por_codigo_funciona()
    {
        $resultado = ResultadosAprendizaje::porCodigo('RAP001')->first();

        $this->assertNotNull($resultado);
        $this->assertEquals('RAP001', $resultado->codigo);
        $this->assertEquals('Resultado de Prueba', $resultado->nombre);
    }

    /**
     * Test: Scope ordenadoPorCodigo funciona correctamente
     */
    public function test_scope_ordenado_por_codigo_funciona()
    {
        ResultadosAprendizaje::create([
            'codigo' => 'RAP003',
            'nombre' => 'RAP 3',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        ResultadosAprendizaje::create([
            'codigo' => 'RAP002',
            'nombre' => 'RAP 2',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $raps = ResultadosAprendizaje::ordenadoPorCodigo()->get();

        $this->assertEquals('RAP001', $raps->first()->codigo);
        $this->assertEquals('RAP003', $raps->last()->codigo);
    }

    /**
     * Test: Atributo getEstadoFormateadoAttribute funciona
     */
    public function test_atributo_estado_formateado_funciona()
    {
        $this->assertEquals('Activo', $this->rap->estadoFormateado);

        $rapInactivo = ResultadosAprendizaje::create([
            'codigo' => 'RAP008',
            'nombre' => 'RAP Inactivo',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 0,
            'user_create_id' => $this->user->id,
            'user_edit_id' => $this->user->id,
        ]);

        $this->assertEquals('Inactivo', $rapInactivo->estadoFormateado);
    }

    /**
     * Test: Atributo getNombreCompletoAttribute funciona
     */
    public function test_atributo_nombre_completo_funciona()
    {
        $this->assertEquals('RAP001 - Resultado de Prueba', $this->rap->nombreCompleto);
    }
}

