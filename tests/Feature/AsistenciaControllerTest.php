<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\AsistenciaAprendiz;
use App\Models\FichaCaracterizacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class AsistenciaControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    /** @test */
    public function puede_ver_listado_de_fichas()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('asistencias.index'));

        $response->assertStatus(200);
        $response->assertViewIs('asistencias.index');
    }

    /** @test */
    public function puede_obtener_asistencias_por_ficha()
    {
        $this->actingAs($this->user);

        $ficha = FichaCaracterizacion::factory()->create();
        AsistenciaAprendiz::factory()->count(5)->create([
            'caracterizacion_id' => $ficha->id
        ]);

        $response = $this->get(route('asistencias.by.ficha', ['ficha' => $ficha->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function puede_obtener_asistencias_por_fecha()
    {
        $this->actingAs($this->user);

        $ficha = FichaCaracterizacion::factory()->create();
        $fecha = Carbon::now()->format('Y-m-d');

        AsistenciaAprendiz::factory()->create([
            'caracterizacion_id' => $ficha->id,
            'created_at' => $fecha,
        ]);

        $response = $this->get(route('asistencias.by.date', [
            'ficha' => $ficha->id,
            'fecha_inicio' => $fecha,
            'fecha_fin' => $fecha,
        ]));

        $response->assertStatus(200);
    }

    /** @test */
    public function puede_registrar_asistencia()
    {
        $this->actingAs($this->user);

        $ficha = FichaCaracterizacion::factory()->create();

        $response = $this->postJson(route('asistencias.store'), [
            'caracterizacion_id' => $ficha->id,
            'nombres' => 'Juan',
            'apellidos' => 'Pérez',
            'numero_identificacion' => '12345678',
            'hora_ingreso' => Carbon::now()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Asistencia guardada con éxito']);
        
        $this->assertDatabaseHas('asistencia_aprendices', [
            'caracterizacion_id' => $ficha->id,
            'numero_identificacion' => '12345678',
        ]);
    }

    /** @test */
    public function puede_actualizar_hora_salida()
    {
        $this->actingAs($this->user);

        $ficha = FichaCaracterizacion::factory()->create();
        $fecha = Carbon::now()->format('Y-m-d');

        AsistenciaAprendiz::factory()->create([
            'caracterizacion_id' => $ficha->id,
            'created_at' => $fecha,
        ]);

        $response = $this->putJson(route('asistencias.update'), [
            'caracterizacion_id' => $ficha->id,
            'hora_salida' => Carbon::now()->format('Y-m-d H:i:s'),
            'fecha' => $fecha,
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
    }

    /** @test */
    public function puede_obtener_asistencias_por_documento()
    {
        $this->actingAs($this->user);

        $documento = '12345678';
        AsistenciaAprendiz::factory()->create([
            'numero_identificacion' => $documento,
        ]);

        $response = $this->get(route('asistencias.by.documento', ['documento' => $documento]));

        $response->assertStatus(200);
    }

    /** @test */
    public function no_puede_registrar_asistencia_con_datos_incompletos()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('asistencias.store'), [
            'nombres' => 'Juan',
            // Faltan datos requeridos
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Datos incompletos']);
    }

    /** @test */
    public function puede_obtener_documentos_por_ficha()
    {
        $this->actingAs($this->user);

        $ficha = FichaCaracterizacion::factory()->create();
        AsistenciaAprendiz::factory()->count(3)->create([
            'caracterizacion_id' => $ficha->id,
        ]);

        $response = $this->get(route('asistencias.documentos', ['ficha' => $ficha->id]));

        $response->assertStatus(200);
    }
}

