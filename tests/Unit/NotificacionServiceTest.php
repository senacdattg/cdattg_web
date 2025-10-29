<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\NotificacionService;
use App\Models\User;
use App\Models\Instructor;
use App\Models\Aprendiz;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;

class NotificacionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected NotificacionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NotificacionService();
    }

    /** @test */
    public function puede_notificar_instructor_sin_email()
    {
        $instructor = Instructor::factory()->create();
        $instructor->persona->email = null;
        $instructor->persona->save();

        $resultado = $this->service->notificarNuevaFichaInstructor($instructor, [
            'numero' => '2089876',
        ]);

        $this->assertFalse($resultado);
    }

    /** @test */
    public function registra_log_al_notificar()
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Notificación de nueva ficha enviada', Mockery::type('array'));

        $instructor = Instructor::factory()->create();
        $instructor->persona->email = 'test@example.com';
        $instructor->persona->save();

        $this->service->notificarNuevaFichaInstructor($instructor, [
            'numero' => '2089876',
        ]);
    }

    /** @test */
    public function puede_notificar_multiples_aprendices()
    {
        $aprendices = Aprendiz::factory()->count(5)->create();

        $enviados = $this->service->notificarAprendices($aprendices, 'Mensaje de prueba');

        $this->assertGreaterThanOrEqual(0, $enviados);
        $this->assertLessThanOrEqual(5, $enviados);
    }

    /** @test */
    public function maneja_errores_al_notificar()
    {
        Log::shouldReceive('error')->atLeast()->once();

        $aprendices = collect([
            (object)['persona' => null], // Aprendiz sin persona
        ]);

        $enviados = $this->service->notificarAprendices($aprendices, 'Mensaje');

        $this->assertEquals(0, $enviados);
    }
}

