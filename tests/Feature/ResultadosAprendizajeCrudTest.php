<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\ResultadosAprendizaje;
use App\Models\Competencia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ResultadosAprendizajeCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $superAdmin;
    protected $competencia;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear permisos necesarios
        Permission::create(['name' => 'VER RESULTADO APRENDIZAJE']);
        Permission::create(['name' => 'CREAR RESULTADO APRENDIZAJE']);
        Permission::create(['name' => 'EDITAR RESULTADO APRENDIZAJE']);
        Permission::create(['name' => 'ELIMINAR RESULTADO APRENDIZAJE']);
        Permission::create(['name' => 'GESTIONAR COMPETENCIAS RAP']);

        // Crear rol SUPER ADMINISTRADOR
        $role = Role::create(['name' => 'SUPER ADMINISTRADOR']);
        $role->givePermissionTo(Permission::all());

        // Crear usuario con rol
        $this->superAdmin = User::factory()->create();
        $this->superAdmin->assignRole('SUPER ADMINISTRADOR');

        // Crear una competencia de prueba
        $this->competencia = Competencia::create([
            'codigo' => 'COMP001',
            'nombre' => 'Competencia de Prueba',
            'descripcion' => 'Descripción de prueba',
            'duracion' => 100,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(6)->format('Y-m-d'),
            'user_create_id' => $this->superAdmin->id,
            'user_edit_id' => $this->superAdmin->id,
        ]);
    }

    /**
     * Test: Acceder a la lista de resultados de aprendizaje
     */
    public function test_puede_acceder_al_listado_de_resultados()
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('resultados-aprendizaje.index'));

        $response->assertStatus(200);
        $response->assertViewIs('resultados_aprendizaje.index');
        $response->assertViewHas('resultadosAprendizaje');
        $response->assertViewHas('competencias');
    }

    /**
     * Test: Crear un resultado de aprendizaje válido
     */
    public function test_puede_crear_resultado_de_aprendizaje_valido()
    {
        $this->actingAs($this->superAdmin);

        $data = [
            'codigo' => 'RAP001',
            'nombre' => 'Resultado de Aprendizaje de Prueba',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'competencia_id' => $this->competencia->id,
            'status' => 1,
        ];

        $response = $this->post(route('resultados-aprendizaje.store'), $data);

        $response->assertRedirect(route('resultados-aprendizaje.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('resultados_aprendizajes', [
            'codigo' => 'RAP001',
            'nombre' => 'Resultado de Aprendizaje de Prueba',
            'duracion' => 40,
            'status' => 1,
        ]);
    }

    /**
     * Test: No puede crear RAP con código duplicado
     */
    public function test_no_puede_crear_resultado_con_codigo_duplicado()
    {
        $this->actingAs($this->superAdmin);

        // Crear primer RAP
        ResultadosAprendizaje::create([
            'codigo' => 'RAP001',
            'nombre' => 'Primer RAP',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->superAdmin->id,
            'user_edit_id' => $this->superAdmin->id,
        ]);

        // Intentar crear segundo con mismo código
        $data = [
            'codigo' => 'RAP001',
            'nombre' => 'Segundo RAP',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
        ];

        $response = $this->post(route('resultados-aprendizaje.store'), $data);

        $response->assertSessionHasErrors('codigo');
    }

    /**
     * Test: No puede crear RAP con duración menor a 1 hora
     */
    public function test_no_puede_crear_resultado_con_duracion_menor_a_uno()
    {
        $this->actingAs($this->superAdmin);

        $data = [
            'codigo' => 'RAP002',
            'nombre' => 'RAP con duración inválida',
            'duracion' => 0,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
        ];

        $response = $this->post(route('resultados-aprendizaje.store'), $data);

        $response->assertSessionHasErrors('duracion');
    }

    /**
     * Test: No puede crear RAP con fechas incoherentes
     */
    public function test_no_puede_crear_resultado_con_fechas_incoherentes()
    {
        $this->actingAs($this->superAdmin);

        $data = [
            'codigo' => 'RAP003',
            'nombre' => 'RAP con fechas inválidas',
            'duracion' => 40,
            'fecha_inicio' => now()->addMonths(3)->format('Y-m-d'),
            'fecha_fin' => now()->format('Y-m-d'),
            'status' => 1,
        ];

        $response = $this->post(route('resultados-aprendizaje.store'), $data);

        $response->assertSessionHasErrors(['fecha_inicio', 'fecha_fin']);
    }

    /**
     * Test: Puede editar un resultado de aprendizaje
     */
    public function test_puede_editar_resultado_de_aprendizaje()
    {
        $this->actingAs($this->superAdmin);

        $rap = ResultadosAprendizaje::create([
            'codigo' => 'RAP004',
            'nombre' => 'RAP Original',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->superAdmin->id,
            'user_edit_id' => $this->superAdmin->id,
        ]);

        $data = [
            'codigo' => 'RAP004',
            'nombre' => 'RAP Modificado',
            'duracion' => 60,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(4)->format('Y-m-d'),
            'status' => 1,
        ];

        $response = $this->put(route('resultados-aprendizaje.update', $rap->id), $data);

        $response->assertRedirect(route('resultados-aprendizaje.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseHas('resultados_aprendizajes', [
            'id' => $rap->id,
            'codigo' => 'RAP004',
            'nombre' => 'RAP Modificado',
            'duracion' => 60,
        ]);
    }

    /**
     * Test: Puede ver detalles de un RAP
     */
    public function test_puede_ver_detalles_de_resultado()
    {
        $this->actingAs($this->superAdmin);

        $rap = ResultadosAprendizaje::create([
            'codigo' => 'RAP005',
            'nombre' => 'RAP para Ver',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->superAdmin->id,
            'user_edit_id' => $this->superAdmin->id,
        ]);

        $response = $this->get(route('resultados-aprendizaje.show', $rap->id));

        $response->assertStatus(200);
        $response->assertViewIs('resultados_aprendizaje.show');
        $response->assertViewHas('resultadoAprendizaje');
        $response->assertSee('RAP005');
        $response->assertSee('RAP para Ver');
    }

    /**
     * Test: Puede cambiar estado de un RAP
     */
    public function test_puede_cambiar_estado_de_resultado()
    {
        $this->actingAs($this->superAdmin);

        $rap = ResultadosAprendizaje::create([
            'codigo' => 'RAP006',
            'nombre' => 'RAP para Cambiar Estado',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->superAdmin->id,
            'user_edit_id' => $this->superAdmin->id,
        ]);

        $response = $this->put(route('resultados-aprendizaje.cambiarEstado', $rap->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');
        
        $rap->refresh();
        $this->assertEquals(0, $rap->status);
    }

    /**
     * Test: Puede eliminar RAP sin guías asociadas
     */
    public function test_puede_eliminar_resultado_sin_guias()
    {
        $this->actingAs($this->superAdmin);

        $rap = ResultadosAprendizaje::create([
            'codigo' => 'RAP007',
            'nombre' => 'RAP para Eliminar',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->superAdmin->id,
            'user_edit_id' => $this->superAdmin->id,
        ]);

        $response = $this->delete(route('resultados-aprendizaje.destroy', $rap->id));

        $response->assertRedirect(route('resultados-aprendizaje.index'));
        $response->assertSessionHas('success');
        
        $this->assertDatabaseMissing('resultados_aprendizajes', [
            'id' => $rap->id,
        ]);
    }

    /**
     * Test: Búsqueda con filtros
     */
    public function test_puede_buscar_resultados_con_filtros()
    {
        $this->actingAs($this->superAdmin);

        // Crear varios RAPs de prueba
        ResultadosAprendizaje::create([
            'codigo' => 'RAP008',
            'nombre' => 'Desarrollo Web',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->superAdmin->id,
            'user_edit_id' => $this->superAdmin->id,
        ]);

        ResultadosAprendizaje::create([
            'codigo' => 'RAP009',
            'nombre' => 'Desarrollo Móvil',
            'duracion' => 60,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 0,
            'user_create_id' => $this->superAdmin->id,
            'user_edit_id' => $this->superAdmin->id,
        ]);

        // Buscar por término
        $response = $this->get(route('resultados-aprendizaje.index', ['search' => 'Web']));
        $response->assertStatus(200);
        $response->assertSee('RAP008');

        // Filtrar por estado
        $response = $this->get(route('resultados-aprendizaje.index', ['status' => 1]));
        $response->assertStatus(200);
        $response->assertSee('RAP008');
    }

    /**
     * Test: API de búsqueda AJAX
     */
    public function test_api_busqueda_funciona_correctamente()
    {
        $this->actingAs($this->superAdmin);

        ResultadosAprendizaje::create([
            'codigo' => 'RAP010',
            'nombre' => 'API Test',
            'duracion' => 40,
            'fecha_inicio' => now()->format('Y-m-d'),
            'fecha_fin' => now()->addMonths(3)->format('Y-m-d'),
            'status' => 1,
            'user_create_id' => $this->superAdmin->id,
            'user_edit_id' => $this->superAdmin->id,
        ]);

        $response = $this->getJson(route('resultados-aprendizaje.search', ['q' => 'API']));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data',
            'pagination'
        ]);
        $response->assertJson([
            'success' => true,
        ]);
    }
}

