<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

class JornadaValidationService
{
    /**
     * Valida si una hora está dentro de una jornada específica
     *
     * @param string|Carbon $hora
     * @param string $jornada
     * @return bool
     */
    public function validarHorarioJornada($hora, string $jornada): bool
    {
        $horarios = $this->obtenerHorariosJornada($jornada);
        
        if (!$horarios) {
            return false;
        }

        $horaValidar = $this->parsearHora($hora);
        $horaInicio = $this->parsearHora($horarios['inicio']);
        $horaFin = $this->parsearHora($horarios['fin']);

        return $horaValidar->between($horaInicio, $horaFin);
    }

    /**
     * Valida si dos horas están dentro de la misma jornada
     *
     * @param string|Carbon $horaIngreso
     * @param string|Carbon $horaActual
     * @param string $jornada
     * @return bool
     */
    public function validarAsistenciaEnJornada($horaIngreso, $horaActual, string $jornada): bool
    {
        $horarios = $this->obtenerHorariosJornada($jornada);
        
        if (!$horarios) {
            return false;
        }

        $horaIngresoCarbon = $this->parsearHora($horaIngreso);
        $horaActualCarbon = $this->parsearHora($horaActual);
        $horaInicio = $this->parsearHora($horarios['inicio']);
        $horaFin = $this->parsearHora($horarios['fin']);

        return $horaIngresoCarbon->between($horaInicio, $horaFin) 
            && $horaActualCarbon->between($horaInicio, $horaFin);
    }

    /**
     * Obtiene la jornada correspondiente a una hora dada
     *
     * @param string|Carbon $hora
     * @return string|null
     */
    public function obtenerJornadaPorHora($hora): ?string
    {
        $horaCarbon = $this->parsearHora($hora);
        $jornadas = Config::get('jornadas.horarios', []);

        foreach ($jornadas as $nombreJornada => $horarios) {
            $horaInicio = $this->parsearHora($horarios['inicio']);
            $horaFin = $this->parsearHora($horarios['fin']);

            if ($horaCarbon->between($horaInicio, $horaFin)) {
                return $nombreJornada;
            }
        }

        return null;
    }

    /**
     * Verifica si hay tiempo suficiente de clase
     *
     * @param string|Carbon $horaIngreso
     * @param string|Carbon $horaSalida
     * @return bool
     */
    public function tienetiempoMinimoClase($horaIngreso, $horaSalida): bool
    {
        $tiempoMinimo = Config::get('jornadas.validacion.tiempo_minimo_clase', 45);
        
        $ingreso = $this->parsearHora($horaIngreso);
        $salida = $this->parsearHora($horaSalida);
        
        $diferencia = $salida->diffInMinutes($ingreso);
        
        return $diferencia >= $tiempoMinimo;
    }

    /**
     * Valida si llegó tarde según la tolerancia configurada
     *
     * @param string|Carbon $horaIngreso
     * @param string $jornada
     * @return array ['llegó_tarde' => bool, 'minutos_retraso' => int]
     */
    public function validarLlegadaTarde($horaIngreso, string $jornada): array
    {
        $horarios = $this->obtenerHorariosJornada($jornada);
        
        if (!$horarios) {
            return ['llego_tarde' => false, 'minutos_retraso' => 0];
        }

        $horaIngresoCarbon = $this->parsearHora($horaIngreso);
        $horaInicioJornada = $this->parsearHora($horarios['inicio']);
        $tolerancia = $horarios['tolerancia_entrada'] ?? 15;

        $horaLimiteTolerance = $horaInicioJornada->copy()->addMinutes($tolerancia);

        $llegoTarde = $horaIngresoCarbon->greaterThan($horaLimiteTolerance);
        $minutosRetraso = $llegoTarde ? $horaIngresoCarbon->diffInMinutes($horaInicioJornada) : 0;

        return [
            'llego_tarde' => $llegoTarde,
            'minutos_retraso' => $minutosRetraso,
            'hora_limite' => $horaLimiteTolerance->format('H:i:s'),
        ];
    }

    /**
     * Valida si salió antes según la tolerancia configurada
     *
     * @param string|Carbon $horaSalida
     * @param string $jornada
     * @return array ['salio_temprano' => bool, 'minutos_anticipado' => int]
     */
    public function validarSalidaTemprana($horaSalida, string $jornada): array
    {
        $horarios = $this->obtenerHorariosJornada($jornada);
        
        if (!$horarios) {
            return ['salio_temprano' => false, 'minutos_anticipado' => 0];
        }

        $horaSalidaCarbon = $this->parsearHora($horaSalida);
        $horaFinJornada = $this->parsearHora($horarios['fin']);
        $tolerancia = $horarios['tolerancia_salida'] ?? 10;

        $horaLimiteTolerance = $horaFinJornada->copy()->subMinutes($tolerancia);

        $salioTemprano = $horaSalidaCarbon->lessThan($horaLimiteTolerance);
        $minutosAnticipado = $salioTemprano ? $horaFinJornada->diffInMinutes($horaSalidaCarbon) : 0;

        return [
            'salio_temprano' => $salioTemprano,
            'minutos_anticipado' => $minutosAnticipado,
            'hora_limite' => $horaLimiteTolerance->format('H:i:s'),
        ];
    }

    /**
     * Obtiene los horarios de una jornada específica
     *
     * @param string $jornada
     * @return array|null
     */
    public function obtenerHorariosJornada(string $jornada): ?array
    {
        $jornadas = Config::get('jornadas.horarios', []);
        return $jornadas[$jornada] ?? null;
    }

    /**
     * Obtiene todas las jornadas disponibles
     *
     * @return array
     */
    public function obtenerTodasLasJornadas(): array
    {
        return array_keys(Config::get('jornadas.horarios', []));
    }

    /**
     * Parsea una hora a Carbon
     *
     * @param string|Carbon $hora
     * @return Carbon
     */
    private function parsearHora($hora): Carbon
    {
        if ($hora instanceof Carbon) {
            return $hora->copy();
        }

        // Si es solo hora (HH:mm:ss), crear desde tiempo
        if (preg_match('/^\d{2}:\d{2}:\d{2}$/', $hora)) {
            list($h, $m, $s) = explode(':', $hora);
            return Carbon::createFromTime((int)$h, (int)$m, (int)$s);
        }

        // Si es fecha y hora completa
        return Carbon::parse($hora);
    }

    /**
     * Genera novedad automática de entrada basada en la hora de ingreso
     *
     * @param string|Carbon $horaIngreso
     * @param string $jornada
     * @return string
     */
    public function generarNovedadEntrada($horaIngreso, string $jornada): string
    {
        $validacion = $this->validarLlegadaTarde($horaIngreso, $jornada);

        if (!$validacion['llego_tarde']) {
            return 'Puntual';
        }

        $minutos = $validacion['minutos_retraso'];

        if ($minutos <= 15) {
            return 'Tarde';
        } elseif ($minutos <= 30) {
            return 'Muy tarde';
        } else {
            return 'Falta justificada';
        }
    }

    /**
     * Genera novedad automática de salida basada en la hora de salida
     *
     * @param string|Carbon $horaSalida
     * @param string $jornada
     * @return string
     */
    public function generarNovedadSalida($horaSalida, string $jornada): string
    {
        $validacion = $this->validarSalidaTemprana($horaSalida, $jornada);

        if (!$validacion['salio_temprano']) {
            return 'Normal';
        }

        return 'Anticipada';
    }
}

