<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\AprendizService;
use App\Repositories\AprendizRepository;
use App\Models\Aprendiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class AprendizServiceTest extends TestCase
{
    use RefreshDatabase;

    protected AprendizService $service;
    protected $mockRepository;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockRepository = Mockery::mock(AprendizRepository::class);
        $this->service = new AprendizService($this->mockRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function puede_listar_aprendices_con_filtros()
    {
        $filtros = ['search' => 'Juan', 'per_page' => 15];
        
        $this->mockRepository
            ->shouldReceive('obtenerAprendicesConFiltros')
            ->once()
            ->with($filtros)
            ->andReturn(collect([]));

        $resultado = $this->service->listarConFiltros($filtros);

        $this->assertIsObject($resultado);
    }

    /** @test */
    public function puede_obtener_estadisticas()
    {
        $estadisticasEsperadas = [
            'total' => 100,
            'activos' => 80,
            'inactivos' => 20,
        ];

        $this->mockRepository
            ->shouldReceive('obtenerEstadisticas')
            ->once()
            ->andReturn($estadisticasEsperadas);

        $resultado = $this->service->obtenerEstadisticas();

        $this->assertEquals($estadisticasEsperadas, $resultado);
    }

    /** @test */
    public function puede_verificar_si_persona_es_aprendiz()
    {
        $personaId = 1;

        $this->mockRepository
            ->shouldReceive('esAprendiz')
            ->once()
            ->with($personaId)
            ->andReturn(true);

        $resultado = $this->service->esAprendiz($personaId);

        $this->assertTrue($resultado);
    }

    /** @test */
    public function puede_contar_aprendices_por_ficha()
    {
        $fichaId = 1;

        $this->mockRepository
            ->shouldReceive('contarPorFicha')
            ->once()
            ->with($fichaId)
            ->andReturn(25);

        $resultado = $this->service->contarPorFicha($fichaId);

        $this->assertEquals(25, $resultado);
    }

    /** @test */
    public function formatea_aprendiz_para_api_correctamente()
    {
        $aprendiz = new Aprendiz();
        $aprendiz->id = 1;
        $aprendiz->persona_id = 1;
        $aprendiz->estado = true;

        // Mock de relaciones
        $persona = (object)[
            'nombre_completo' => 'Juan Pérez',
            'numero_documento' => '123456789',
            'email' => 'juan@example.com'
        ];
        
        $ficha = (object)['ficha' => '2089876'];
        $programa = (object)['nombre' => 'ADSI'];
        $ficha->programaFormacion = $programa;

        $aprendiz->setRelation('persona', $persona);
        $aprendiz->setRelation('fichaCaracterizacion', $ficha);

        $resultado = $this->service->formatearParaApi($aprendiz);

        $this->assertIsArray($resultado);
        $this->assertEquals(1, $resultado['id']);
        $this->assertEquals('Juan Pérez', $resultado['nombre_completo']);
        $this->assertEquals('123456789', $resultado['numero_documento']);
        $this->assertEquals('juan@example.com', $resultado['email']);
        $this->assertEquals('2089876', $resultado['ficha']);
        $this->assertEquals('ADSI', $resultado['programa']);
        $this->assertTrue($resultado['estado']);
    }
}

