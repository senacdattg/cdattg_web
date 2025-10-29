<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\FichaCaracterizacion;
use App\Models\AsistenciaAprendiz;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReporteControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    /** @test */
    public function puede_ver_dashboard_de_reportes()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('reportes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('reportes.index');
    }

    /** @test */
    public function puede_obtener_estadisticas_generales_api()
    {
        $this->actingAs($this->user);

        $response = $this->getJson(route('estadisticas.api'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'aprendices',
                'fichas',
                'instructores',
                'asistencias_hoy',
            ]
        ]);
    }

    /** @test */
    public function puede_solicitar_reporte_asistencia()
    {
        $this->actingAs($this->user);

        $ficha = FichaCaracterizacion::factory()->create();
        AsistenciaAprendiz::factory()->count(10)->create();

        $response = $this->postJson(route('reportes.asistencia'), [
            'ficha_id' => $ficha->id,
            'fecha_inicio' => '2024-01-01',
            'fecha_fin' => '2024-01-31',
            'formato' => 'array',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
        ]);
    }

    /** @test */
    public function puede_solicitar_reporte_aprendices()
    {
        $this->actingAs($this->user);

        $ficha = FichaCaracterizacion::factory()->create();

        $response = $this->postJson(route('reportes.aprendices'), [
            'ficha_id' => $ficha->id,
            'formato' => 'array',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
        ]);
    }

    /** @test */
    public function valida_datos_requeridos_para_reporte()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('reportes.asistencia'), [
            // Sin datos requeridos
        ]);

        $response->assertStatus(422);
    }
}

