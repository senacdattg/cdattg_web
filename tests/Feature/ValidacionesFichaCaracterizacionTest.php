<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\FichaCaracterizacion;
use App\Models\ProgramaFormacion;
use App\Models\Ambiente;
use App\Models\Instructor;
use App\Models\Sede;
use App\Services\FichaCaracterizacionValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class ValidacionesFichaCaracterizacionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $validationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validationService = new FichaCaracterizacionValidationService();
    }

    /**
     * Test de validación de disponibilidad de ambiente.
     */
    public function test_validar_disponibilidad_ambiente()
    {
        // Crear ambiente
        $ambiente = Ambiente::factory()->create();

        // Crear ficha existente que usa el ambiente
        $fichaExistente = FichaCaracterizacion::factory()->create([
            'ambiente_id' => $ambiente->id,
            'fecha_inicio' => '2024-01-01',
            'fecha_fin' => '2024-06-30',
            'status' => true
        ]);

        // Datos para nueva ficha con conflicto de fechas
        $datos = [
            'ambiente_id' => $ambiente->id,
            'fecha_inicio' => '2024-03-01',
            'fecha_fin' => '2024-04-30',
            'programa_formacion_id' => 1,
            'ficha' => 'TEST-001'
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('ambiente', strtolower(implode(' ', $resultado['errores'])));
    }

    /**
     * Test de validación de disponibilidad de instructor.
     */
    public function test_validar_disponibilidad_instructor()
    {
        // Crear instructor
        $instructor = Instructor::factory()->create();

        // Crear ficha existente que usa el instructor
        $fichaExistente = FichaCaracterizacion::factory()->create([
            'instructor_id' => $instructor->id,
            'fecha_inicio' => '2024-01-01',
            'fecha_fin' => '2024-06-30',
            'status' => true
        ]);

        // Datos para nueva ficha con conflicto de fechas
        $datos = [
            'instructor_id' => $instructor->id,
            'fecha_inicio' => '2024-03-01',
            'fecha_fin' => '2024-04-30',
            'programa_formacion_id' => 1,
            'ficha' => 'TEST-002'
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('instructor', strtolower(implode(' ', $resultado['errores'])));
    }

    /**
     * Test de validación de unicidad de ficha por programa.
     */
    public function test_validar_ficha_unica_por_programa()
    {
        // Crear programa
        $programa = ProgramaFormacion::factory()->create();

        // Crear ficha existente
        $fichaExistente = FichaCaracterizacion::factory()->create([
            'ficha' => 'TEST-003',
            'programa_formacion_id' => $programa->id
        ]);

        // Datos para nueva ficha con mismo número y programa
        $datos = [
            'ficha' => 'TEST-003',
            'programa_formacion_id' => $programa->id,
            'fecha_inicio' => '2024-01-01',
            'fecha_fin' => '2024-06-30'
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('ficha', strtolower(implode(' ', $resultado['errores'])));
    }

    /**
     * Test de validación de días festivos.
     */
    public function test_validar_fechas_festivos()
    {
        // Datos para ficha que incluye Navidad (25 de diciembre)
        $datos = [
            'fecha_inicio' => '2024-12-20',
            'fecha_fin' => '2024-12-30',
            'programa_formacion_id' => 1,
            'ficha' => 'TEST-004'
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('festivos', strtolower(implode(' ', $resultado['errores'])));
    }

    /**
     * Test de validación de duración mínima.
     */
    public function test_validar_duracion_minima()
    {
        // Datos para ficha con duración menor a 30 días
        $datos = [
            'fecha_inicio' => '2024-01-01',
            'fecha_fin' => '2024-01-15', // Solo 15 días
            'programa_formacion_id' => 1,
            'ficha' => 'TEST-005'
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('duración', strtolower(implode(' ', $resultado['errores'])));
    }

    /**
     * Test de validación de duración máxima.
     */
    public function test_validar_duracion_maxima()
    {
        // Datos para ficha con duración mayor a 2 años
        $datos = [
            'fecha_inicio' => '2024-01-01',
            'fecha_fin' => '2027-01-01', // Más de 2 años
            'programa_formacion_id' => 1,
            'ficha' => 'TEST-006'
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('duración', strtolower(implode(' ', $resultado['errores'])));
    }

    /**
     * Test de validación de fin de semana.
     */
    public function test_validar_fin_de_semana()
    {
        // Datos para ficha que inicia en sábado
        $datos = [
            'fecha_inicio' => '2024-01-06', // Sábado
            'fecha_fin' => '2024-06-30',
            'programa_formacion_id' => 1,
            'ficha' => 'TEST-007'
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('fin de semana', strtolower(implode(' ', $resultado['errores'])));
    }

    /**
     * Test de validación exitosa.
     */
    public function test_validacion_exitosa()
    {
        // Datos válidos para una ficha
        $datos = [
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2024-07-31',
            'programa_formacion_id' => 1,
            'ficha' => 'TEST-008'
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertTrue($resultado['valido']);
        $this->assertEmpty($resultado['errores']);
    }

    /**
     * Test de validación de eliminación de ficha.
     */
    public function test_validar_eliminacion_ficha()
    {
        // Crear ficha sin dependencias
        $ficha = FichaCaracterizacion::factory()->create([
            'fecha_inicio' => '2025-01-01', // Fecha futura
            'status' => true
        ]);

        $resultado = $this->validationService->validarEliminacionFicha($ficha->id);

        $this->assertTrue($resultado['valido']);
    }

    /**
     * Test de validación de edición de ficha.
     */
    public function test_validar_edicion_ficha()
    {
        // Crear ficha
        $ficha = FichaCaracterizacion::factory()->create();

        $resultado = $this->validationService->validarEdicionFicha($ficha->id);

        $this->assertTrue($resultado['valido']);
    }

    /**
     * Test de validación de límite de fichas por instructor.
     */
    public function test_validar_limite_fichas_por_instructor()
    {
        // Crear instructor
        $instructor = Instructor::factory()->create();

        // Crear múltiples fichas para el instructor
        for ($i = 0; $i < 3; $i++) {
            FichaCaracterizacion::factory()->create([
                'instructor_id' => $instructor->id,
                'fecha_inicio' => "2024-0" . ($i + 1) . "-01",
                'fecha_fin' => "2024-0" . ($i + 1) . "-30",
                'status' => true
            ]);
        }

        // Datos para nueva ficha que excedería el límite
        $datos = [
            'instructor_id' => $instructor->id,
            'fecha_inicio' => '2024-02-15',
            'fecha_fin' => '2024-03-15',
            'programa_formacion_id' => 1,
            'ficha' => 'TEST-009'
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('máximo', strtolower(implode(' ', $resultado['errores'])));
    }
}