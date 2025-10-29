<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\JornadaValidationService;
use Carbon\Carbon;

class JornadaValidationServiceTest extends TestCase
{
    protected JornadaValidationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new JornadaValidationService();
    }

    /** @test */
    public function puede_validar_horario_jornada_manana()
    {
        $hora = Carbon::createFromTime(8, 0, 0);
        
        $resultado = $this->service->validarHorarioJornada($hora, 'Mañana');

        $this->assertTrue($resultado);
    }

    /** @test */
    public function puede_validar_horario_jornada_tarde()
    {
        $hora = Carbon::createFromTime(15, 0, 0);
        
        $resultado = $this->service->validarHorarioJornada($hora, 'Tarde');

        $this->assertTrue($resultado);
    }

    /** @test */
    public function puede_validar_horario_jornada_noche()
    {
        $hora = Carbon::createFromTime(20, 0, 0);
        
        $resultado = $this->service->validarHorarioJornada($hora, 'Noche');

        $this->assertTrue($resultado);
    }

    /** @test */
    public function rechaza_hora_fuera_de_jornada()
    {
        $hora = Carbon::createFromTime(2, 0, 0); // 2 AM
        
        $resultado = $this->service->validarHorarioJornada($hora, 'Mañana');

        $this->assertFalse($resultado);
    }

    /** @test */
    public function puede_obtener_jornada_por_hora()
    {
        $horaMañana = Carbon::createFromTime(8, 0, 0);
        $horaTarde = Carbon::createFromTime(15, 0, 0);
        $horaNoche = Carbon::createFromTime(20, 0, 0);

        $this->assertEquals('Mañana', $this->service->obtenerJornadaPorHora($horaMañana));
        $this->assertEquals('Tarde', $this->service->obtenerJornadaPorHora($horaTarde));
        $this->assertEquals('Noche', $this->service->obtenerJornadaPorHora($horaNoche));
    }

    /** @test */
    public function puede_validar_asistencia_en_jornada()
    {
        $horaIngreso = Carbon::createFromTime(8, 0, 0);
        $horaActual = Carbon::createFromTime(9, 0, 0);

        $resultado = $this->service->validarAsistenciaEnJornada($horaIngreso, $horaActual, 'Mañana');

        $this->assertTrue($resultado);
    }

    /** @test */
    public function detecta_llegada_tarde()
    {
        // Jornada Mañana comienza a las 06:00, tolerancia 15 minutos
        $horaIngreso = Carbon::createFromTime(6, 30, 0); // 30 minutos tarde

        $resultado = $this->service->validarLlegadaTarde($horaIngreso, 'Mañana');

        $this->assertTrue($resultado['llego_tarde']);
        $this->assertEquals(30, $resultado['minutos_retraso']);
    }

    /** @test */
    public function detecta_llegada_puntual()
    {
        $horaIngreso = Carbon::createFromTime(6, 10, 0); // Dentro de tolerancia

        $resultado = $this->service->validarLlegadaTarde($horaIngreso, 'Mañana');

        $this->assertFalse($resultado['llego_tarde']);
        $this->assertEquals(0, $resultado['minutos_retraso']);
    }

    /** @test */
    public function detecta_salida_temprana()
    {
        // Jornada Mañana termina a las 13:10, tolerancia 10 minutos
        $horaSalida = Carbon::createFromTime(12, 0, 0); // Salió 1h 10min antes

        $resultado = $this->service->validarSalidaTemprana($horaSalida, 'Mañana');

        $this->assertTrue($resultado['salio_temprano']);
        $this->assertGreaterThan(0, $resultado['minutos_anticipado']);
    }

    /** @test */
    public function genera_novedad_entrada_correcta()
    {
        $horaPuntual = Carbon::createFromTime(6, 5, 0);
        $horaTarde = Carbon::createFromTime(6, 20, 0);

        $this->assertEquals('Puntual', $this->service->generarNovedadEntrada($horaPuntual, 'Mañana'));
        $this->assertEquals('Tarde', $this->service->generarNovedadEntrada($horaTarde, 'Mañana'));
    }

    /** @test */
    public function obtiene_todas_las_jornadas()
    {
        $jornadas = $this->service->obtenerTodasLasJornadas();

        $this->assertIsArray($jornadas);
        $this->assertContains('Mañana', $jornadas);
        $this->assertContains('Tarde', $jornadas);
        $this->assertContains('Noche', $jornadas);
    }

    /** @test */
    public function obtiene_horarios_de_jornada()
    {
        $horarios = $this->service->obtenerHorariosJornada('Mañana');

        $this->assertIsArray($horarios);
        $this->assertArrayHasKey('inicio', $horarios);
        $this->assertArrayHasKey('fin', $horarios);
        $this->assertArrayHasKey('tolerancia_entrada', $horarios);
        $this->assertArrayHasKey('tolerancia_salida', $horarios);
    }

    /** @test */
    public function retorna_null_para_jornada_inexistente()
    {
        $horarios = $this->service->obtenerHorariosJornada('Madrugada');

        $this->assertNull($horarios);
    }
}

