<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ReporteService;
use App\Repositories\AsistenciaAprendizRepository;
use App\Repositories\AprendizRepository;
use App\Repositories\FichaRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class ReporteServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ReporteService $service;
    protected $mockAsistenciaRepo;
    protected $mockAprendizRepo;
    protected $mockFichaRepo;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockAsistenciaRepo = Mockery::mock(AsistenciaAprendizRepository::class);
        $this->mockAprendizRepo = Mockery::mock(AprendizRepository::class);
        $this->mockFichaRepo = Mockery::mock(FichaRepository::class);
        
        $this->service = new ReporteService(
            $this->mockAsistenciaRepo,
            $this->mockAprendizRepo,
            $this->mockFichaRepo
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function puede_generar_reporte_de_asistencia()
    {
        $fichaId = 1;
        $fechaInicio = '2024-01-01';
        $fechaFin = '2024-01-31';

        $this->mockAsistenciaRepo
            ->shouldReceive('obtenerPorFichaYFechas')
            ->once()
            ->andReturn(collect([]));

        $this->mockAsistenciaRepo
            ->shouldReceive('obtenerEstadisticas')
            ->once()
            ->andReturn(['total_registros' => 0]);

        $this->mockFichaRepo
            ->shouldReceive('encontrarConRelaciones')
            ->once()
            ->andReturn((object)[
                'ficha' => '2089876',
                'programaFormacion' => (object)['nombre' => 'ADSI'],
                'jornadaFormacion' => (object)['jornada' => 'Mañana'],
            ]);

        $resultado = $this->service->generarReporteAsistencia($fichaId, $fechaInicio, $fechaFin, 'array');

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('ficha', $resultado);
        $this->assertArrayHasKey('periodo', $resultado);
        $this->assertArrayHasKey('estadisticas', $resultado);
    }

    /** @test */
    public function puede_generar_reporte_de_aprendices()
    {
        $fichaId = 1;

        $this->mockAprendizRepo
            ->shouldReceive('obtenerPorFicha')
            ->once()
            ->andReturn(collect([]));

        $this->mockFichaRepo
            ->shouldReceive('encontrarConRelaciones')
            ->once()
            ->andReturn((object)[
                'ficha' => '2089876',
                'programaFormacion' => (object)['nombre' => 'ADSI'],
            ]);

        $resultado = $this->service->generarReporteAprendices($fichaId, 'array');

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('total_aprendices', $resultado);
        $this->assertArrayHasKey('aprendices_activos', $resultado);
    }

    /** @test */
    public function puede_generar_reporte_consolidado_mes()
    {
        $mes = 1;
        $anio = 2024;

        $this->mockFichaRepo
            ->shouldReceive('obtenerVigentes')
            ->once()
            ->andReturn(collect([]));

        $resultado = $this->service->generarReporteConsolidadoMes($mes, $anio);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('periodo', $resultado);
        $this->assertArrayHasKey('total_fichas', $resultado);
        $this->assertArrayHasKey('fichas', $resultado);
    }
}

