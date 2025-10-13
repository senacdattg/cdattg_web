<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Aprendiz;
use App\Models\Persona;
use App\Models\FichaCaracterizacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class AprendizControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Crear usuario con permisos
        $this->user = User::factory()->create();
        $this->user->givePermissionTo('VER APRENDIZ');
    }

    /** @test */
    public function puede_ver_listado_de_aprendices()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('aprendices.index'));

        $response->assertStatus(200);
        $response->assertViewIs('aprendices.index');
        $response->assertViewHas('aprendices');
    }

    /** @test */
    public function puede_buscar_aprendices_por_nombre()
    {
        $this->actingAs($this->user);

        $persona = Persona::factory()->create([
            'primer_nombre' => 'Juan',
            'primer_apellido' => 'Pérez'
        ]);
        
        Aprendiz::factory()->create(['persona_id' => $persona->id]);

        $response = $this->get(route('aprendices.index', ['search' => 'Juan']));

        $response->assertStatus(200);
        $response->assertSee('Juan');
    }

    /** @test */
    public function puede_filtrar_aprendices_por_ficha()
    {
        $this->actingAs($this->user);
        
        $ficha = FichaCaracterizacion::factory()->create();
        Aprendiz::factory()->create(['ficha_caracterizacion_id' => $ficha->id]);

        $response = $this->get(route('aprendices.index', ['ficha_id' => $ficha->id]));

        $response->assertStatus(200);
    }

    /** @test */
    public function puede_crear_aprendiz()
    {
        $this->user->givePermissionTo('CREAR APRENDIZ');
        $this->actingAs($this->user);

        $persona = Persona::factory()->create();
        $ficha = FichaCaracterizacion::factory()->create();

        $response = $this->post(route('aprendices.store'), [
            'persona_id' => $persona->id,
            'ficha_caracterizacion_id' => $ficha->id,
            'estado' => true,
        ]);

        $response->assertRedirect(route('aprendices.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('aprendices', [
            'persona_id' => $persona->id,
            'ficha_caracterizacion_id' => $ficha->id,
        ]);
    }

    /** @test */
    public function puede_ver_detalles_de_aprendiz()
    {
        $this->actingAs($this->user);

        $aprendiz = Aprendiz::factory()->create();

        $response = $this->get(route('aprendices.show', $aprendiz->id));

        $response->assertStatus(200);
        $response->assertViewIs('aprendices.show');
        $response->assertViewHas('aprendiz');
    }

    /** @test */
    public function puede_actualizar_aprendiz()
    {
        $this->user->givePermissionTo('EDITAR APRENDIZ');
        $this->actingAs($this->user);

        $aprendiz = Aprendiz::factory()->create();
        $nuevaFicha = FichaCaracterizacion::factory()->create();

        $response = $this->put(route('aprendices.update', $aprendiz->id), [
            'persona_id' => $aprendiz->persona_id,
            'ficha_caracterizacion_id' => $nuevaFicha->id,
            'estado' => true,
        ]);

        $response->assertRedirect(route('aprendices.show', $aprendiz->id));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('aprendices', [
            'id' => $aprendiz->id,
            'ficha_caracterizacion_id' => $nuevaFicha->id,
        ]);
    }

    /** @test */
    public function puede_eliminar_aprendiz()
    {
        $this->user->givePermissionTo('ELIMINAR APRENDIZ');
        $this->actingAs($this->user);

        $aprendiz = Aprendiz::factory()->create();

        $response = $this->delete(route('aprendices.destroy', $aprendiz->id));

        $response->assertRedirect(route('aprendices.index'));
        $response->assertSessionHas('success');
        
        $this->assertSoftDeleted('aprendices', [
            'id' => $aprendiz->id,
        ]);
    }

    /** @test */
    public function api_puede_listar_aprendices()
    {
        $this->actingAs($this->user);

        Aprendiz::factory()->count(5)->create();

        $response = $this->getJson(route('aprendices.api.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'aprendices' => [
                '*' => ['id', 'nombre_completo', 'numero_documento', 'email', 'ficha', 'programa', 'estado']
            ]
        ]);
    }

    /** @test */
    public function api_puede_buscar_aprendices()
    {
        $this->actingAs($this->user);

        $persona = Persona::factory()->create(['primer_nombre' => 'María']);
        Aprendiz::factory()->create(['persona_id' => $persona->id]);

        $response = $this->getJson(route('aprendices.search', ['q' => 'María']));

        $response->assertStatus(200);
        $response->assertJsonFragment(['nombre_completo' => $persona->nombre_completo]);
    }
}

