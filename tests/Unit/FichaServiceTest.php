<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\FichaService;
use App\Repositories\FichaRepository;
use App\Repositories\InstructorFichaRepository;
use App\Repositories\AprendizFichaRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class FichaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected FichaService $service;
    protected $mockFichaRepo;
    protected $mockInstructorFichaRepo;
    protected $mockAprendizFichaRepo;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->mockFichaRepo = Mockery::mock(FichaRepository::class);
        $this->mockInstructorFichaRepo = Mockery::mock(InstructorFichaRepository::class);
        $this->mockAprendizFichaRepo = Mockery::mock(AprendizFichaRepository::class);
        
        $this->service = new FichaService(
            $this->mockFichaRepo,
            $this->mockInstructorFichaRepo,
            $this->mockAprendizFichaRepo
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function puede_obtener_estadisticas()
    {
        $estadisticasEsperadas = [
            'total' => 100,
            'activas' => 80,
            'vigentes' => 60,
        ];

        $this->mockFichaRepo
            ->shouldReceive('obtenerEstadisticas')
            ->once()
            ->andReturn($estadisticasEsperadas);

        $resultado = $this->service->obtenerEstadisticas();

        $this->assertEquals($estadisticasEsperadas, $resultado);
    }

    /** @test */
    public function puede_verificar_disponibilidad()
    {
        $fichaId = 1;

        $this->mockFichaRepo
            ->shouldReceive('encontrarConRelaciones')
            ->once()
            ->andReturn((object)[
                'id' => $fichaId,
                'status' => true,
                'cupos_maximos' => 40,
            ]);

        $this->mockInstructorFichaRepo
            ->shouldReceive('obtenerPorFicha')
            ->once()
            ->andReturn(collect([]));

        $this->mockAprendizFichaRepo
            ->shouldReceive('obtenerPorFicha')
            ->once()
            ->andReturn(collect([]));

        $resultado = $this->service->verificarDisponibilidad($fichaId);

        $this->assertIsArray($resultado);
        $this->assertArrayHasKey('disponible', $resultado);
        $this->assertArrayHasKey('total_instructores', $resultado);
        $this->assertArrayHasKey('total_aprendices', $resultado);
    }
}

