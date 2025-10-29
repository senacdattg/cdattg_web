<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Repositories\FichaRepository;
use App\Models\FichaCaracterizacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class FichaRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected FichaRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new FichaRepository();
        Cache::flush();
    }

    /** @test */
    public function puede_obtener_fichas_activas()
    {
        FichaCaracterizacion::factory()->count(3)->create(['status' => 1]);
        FichaCaracterizacion::factory()->count(2)->create(['status' => 0]);

        $fichas = $this->repository->obtenerActivas();

        $this->assertCount(3, $fichas);
    }

    /** @test */
    public function cachea_fichas_activas()
    {
        FichaCaracterizacion::factory()->create(['status' => 1]);

        // Primera llamada (sin caché)
        $fichas1 = $this->repository->obtenerActivas();

        // Segunda llamada (con caché)
        $fichas2 = $this->repository->obtenerActivas();

        $this->assertEquals($fichas1->count(), $fichas2->count());
    }

    /** @test */
    public function puede_obtener_estadisticas()
    {
        FichaCaracterizacion::factory()->count(5)->create(['status' => 1]);
        FichaCaracterizacion::factory()->count(2)->create(['status' => 0]);

        $estadisticas = $this->repository->obtenerEstadisticas();

        $this->assertArrayHasKey('total', $estadisticas);
        $this->assertArrayHasKey('activas', $estadisticas);
        $this->assertEquals(7, $estadisticas['total']);
        $this->assertEquals(5, $estadisticas['activas']);
    }

    /** @test */
    public function invalida_cache_al_modificar()
    {
        $ficha = FichaCaracterizacion::factory()->create(['status' => 1]);

        // Cachear
        $this->repository->obtenerActivas();

        // Invalidar
        $this->repository->invalidarCache();

        // Verificar que se puede cachear nuevamente
        $fichas = $this->repository->obtenerActivas();

        $this->assertCount(1, $fichas);
    }
}

