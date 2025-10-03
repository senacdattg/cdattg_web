<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\FichaCaracterizacion;
use App\Models\ProgramaFormacion;
use App\Models\Ambiente;
use App\Models\Instructor;
use App\Models\Sede;
use App\Models\JornadaFormacion;
use App\Models\ModalidadFormacion;
use App\Services\FichaCaracterizacionValidationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Carbon\Carbon;

class ValidacionesFichaCaracterizacionTest extends TestCase
{
    use RefreshDatabase;

    protected $validationService;
    protected $programa;
    protected $ambiente;
    protected $instructor;
    protected $sede;
    protected $jornada;
    protected $modalidad;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->validationService = new FichaCaracterizacionValidationService();
        
        // Crear datos de prueba
        $this->programa = ProgramaFormacion::factory()->create([
            'nombre' => 'Técnico en Programación de Software',
            'nivel' => 'TÉCNICO'
        ]);

        $this->sede = Sede::factory()->create([
            'nombre' => 'Centro de Formación Test',
            'regional_id' => 1
        ]);

        $this->ambiente = Ambiente::factory()->create([
            'nombre_ambiente' => 'Laboratorio de Programación',
            'sede_id' => $this->sede->id,
            'capacidad_maxima_horas' => 40
        ]);

        $this->instructor = Instructor::factory()->create([
            'persona_id' => 1,
            'regional_id' => $this->sede->regional_id
        ]);

        $this->jornada = JornadaFormacion::factory()->create([
            'jornada' => 'MAÑANA'
        ]);

        $this->modalidad = ModalidadFormacion::factory()->create([
            'modalidad' => 'PRESENCIAL'
        ]);
    }

    /** @test */
    public function puede_validar_disponibilidad_de_ambiente()
    {
        // Crear una ficha existente
        $fichaExistente = FichaCaracterizacion::factory()->create([
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2024-06-01',
            'status' => true
        ]);

        // Datos para nueva ficha con conflicto de ambiente
        $datos = [
            'ficha' => 'TEST-002',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-03-01', // Se superpone con la ficha existente
            'fecha_fin' => '2024-07-01',
            'instructor_id' => $this->instructor->id,
            'sede_id' => $this->sede->id,
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('ambiente no está disponible', $resultado['errores'][0]);
    }

    /** @test */
    public function puede_validar_disponibilidad_de_instructor()
    {
        // Crear una ficha existente
        $fichaExistente = FichaCaracterizacion::factory()->create([
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'instructor_id' => $this->instructor->id,
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2024-06-01',
            'status' => true
        ]);

        // Datos para nueva ficha con conflicto de instructor
        $datos = [
            'ficha' => 'TEST-002',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-03-01', // Se superpone con la ficha existente
            'fecha_fin' => '2024-07-01',
            'instructor_id' => $this->instructor->id, // Mismo instructor
            'sede_id' => $this->sede->id,
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('instructor no está disponible', $resultado['errores'][0]);
    }

    /** @test */
    public function puede_validar_unicidad_de_ficha_por_programa()
    {
        // Crear una ficha existente
        $fichaExistente = FichaCaracterizacion::factory()->create([
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'status' => true
        ]);

        // Datos para nueva ficha con mismo número en mismo programa
        $datos = [
            'ficha' => 'TEST-001', // Mismo número de ficha
            'programa_formacion_id' => $this->programa->id, // Mismo programa
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-07-01',
            'fecha_fin' => '2024-11-01',
            'instructor_id' => $this->instructor->id,
            'sede_id' => $this->sede->id,
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('Ya existe una ficha con el número', $resultado['errores'][0]);
    }

    /** @test */
    public function puede_validar_reglas_de_negocio_sena()
    {
        // Datos con fecha de fin de semana
        $datos = [
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-02-03', // Sábado
            'fecha_fin' => '2024-06-01',
            'instructor_id' => $this->instructor->id,
            'sede_id' => $this->sede->id,
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('fin de semana', $resultado['errores'][0]);
    }

    /** @test */
    public function puede_validar_duracion_minima_del_programa()
    {
        // Datos con duración menor a 30 días
        $datos = [
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2024-02-15', // Solo 14 días
            'instructor_id' => $this->instructor->id,
            'sede_id' => $this->sede->id,
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('duración mínima', $resultado['errores'][0]);
    }

    /** @test */
    public function puede_validar_duracion_maxima_del_programa()
    {
        // Datos con duración mayor a 2 años
        $datos = [
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2026-02-01', // Más de 2 años
            'instructor_id' => $this->instructor->id,
            'sede_id' => $this->sede->id,
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('duración máxima', $resultado['errores'][0]);
    }

    /** @test */
    public function puede_validar_ambiente_pertenece_a_sede()
    {
        // Crear ambiente de otra sede
        $otraSede = Sede::factory()->create(['regional_id' => 2]);
        $ambienteOtraSede = Ambiente::factory()->create([
            'sede_id' => $otraSede->id
        ]);

        $datos = [
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $ambienteOtraSede->id, // Ambiente de otra sede
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2024-06-01',
            'instructor_id' => $this->instructor->id,
            'sede_id' => $this->sede->id, // Sede diferente
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('ambiente seleccionado no pertenece', $resultado['errores'][0]);
    }

    /** @test */
    public function puede_validar_instructor_pertenece_a_regional()
    {
        // Crear instructor de otra regional
        $instructorOtraRegional = Instructor::factory()->create([
            'regional_id' => 2 // Diferente regional
        ]);

        $datos = [
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2024-06-01',
            'instructor_id' => $instructorOtraRegional->id, // Instructor de otra regional
            'sede_id' => $this->sede->id,
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertFalse($resultado['valido']);
        $this->assertStringContainsString('instructor debe pertenecer a la misma regional', $resultado['errores'][0]);
    }

    /** @test */
    public function puede_validar_ficha_valida()
    {
        // Datos válidos
        $datos = [
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2024-06-01',
            'instructor_id' => $this->instructor->id,
            'sede_id' => $this->sede->id,
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos);

        $this->assertTrue($resultado['valido']);
        $this->assertEmpty($resultado['errores']);
    }

    /** @test */
    public function puede_actualizar_ficha_sin_conflicto_con_si_misma()
    {
        // Crear una ficha existente
        $fichaExistente = FichaCaracterizacion::factory()->create([
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'instructor_id' => $this->instructor->id,
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2024-06-01',
            'status' => true
        ]);

        // Datos para actualizar la misma ficha
        $datos = [
            'ficha' => 'TEST-001',
            'programa_formacion_id' => $this->programa->id,
            'ambiente_id' => $this->ambiente->id,
            'fecha_inicio' => '2024-02-01',
            'fecha_fin' => '2024-06-01',
            'instructor_id' => $this->instructor->id,
            'sede_id' => $this->sede->id,
            'jornada_id' => $this->jornada->id,
            'modalidad_formacion_id' => $this->modalidad->id,
            'total_horas' => 800
        ];

        $resultado = $this->validationService->validarFichaCompleta($datos, $fichaExistente->id);

        $this->assertTrue($resultado['valido']);
        $this->assertEmpty($resultado['errores']);
    }
}
