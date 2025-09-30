<?php

namespace App\Console\Commands;

use App\Events\NuevaAsistenciaRegistrada;
use App\Models\AprendizFicha;
use App\Models\AsistenciaAprendiz;
use App\Models\InstructorFichaCaracterizacion;
use Carbon\Carbon;
use Illuminate\Console\Command;

class RegistrarAsistenciaPrueba extends Command
{
    /**
     * Nombre y firma del comando de consola.
     *
     * @var string
     */
    protected $signature = 'asistencia:registrar {tipo=entrada : entrada o salida}';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Registra una asistencia de prueba en la base de datos';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle()
    {
        $tipo = $this->argument('tipo');

        if (!in_array($tipo, ['entrada', 'salida'])) {
            $this->error('Tipo no válido. Use "entrada" o "salida"');
            return 1;
        }

        try {
            // Obtener el primer aprendiz_ficha disponible
            $aprendizFicha = AprendizFicha::with(['aprendiz.persona'])->first();
            
            if (!$aprendizFicha) {
                $this->error('No se encontró ningún aprendiz en la base de datos.');
                $this->info('Por favor, crea al menos un aprendiz primero.');
                return 1;
            }

            // Obtener el primer instructor_ficha disponible
            $instructorFicha = InstructorFichaCaracterizacion::first();
            
            if (!$instructorFicha) {
                $this->error('No se encontró ningún instructor asignado a una ficha.');
                $this->info('Por favor, crea al menos una asignación de instructor a ficha primero.');
                return 1;
            }

            if ($tipo === 'entrada') {
                // Registrar entrada
                $asistencia = AsistenciaAprendiz::create([
                    'instructor_ficha_id' => $instructorFicha->id,
                    'aprendiz_ficha_id' => $aprendizFicha->id,
                    'evidencia_id' => null,
                    'hora_ingreso' => Carbon::now()->format('H:i:s'),
                    'hora_salida' => null,
                ]);

                $this->info('✓ Asistencia de ENTRADA registrada con éxito!');
            } else {
                // Buscar última asistencia sin salida
                $asistencia = AsistenciaAprendiz::where('aprendiz_ficha_id', $aprendizFicha->id)
                    ->whereNull('hora_salida')
                    ->whereDate('created_at', Carbon::today())
                    ->latest()
                    ->first();

                if (!$asistencia) {
                    $this->error('No se encontró una asistencia de entrada para registrar la salida.');
                    $this->info('Primero registra una entrada con: php artisan asistencia:registrar entrada');
                    return 1;
                }

                // Registrar salida
                $asistencia->hora_salida = Carbon::now()->format('H:i:s');
                $asistencia->save();

                $this->info('✓ Asistencia de SALIDA registrada con éxito!');
            }

            // Obtener nombre del aprendiz
            $nombreAprendiz = $aprendizFicha->aprendiz->persona->getNombreCompletoAttribute();

            // Mostrar información
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['ID', $asistencia->id],
                    ['Aprendiz', $nombreAprendiz],
                    ['Tipo', strtoupper($tipo)],
                    ['Hora Ingreso', $asistencia->hora_ingreso],
                    ['Hora Salida', $asistencia->hora_salida ?? 'Pendiente'],
                    ['Fecha', $asistencia->created_at->format('Y-m-d H:i:s')],
                ]
            );

            // Disparar evento de WebSocket
            event(new NuevaAsistenciaRegistrada([
                'id' => $asistencia->id,
                'aprendiz' => $nombreAprendiz,
                'estado' => $tipo,
                'timestamp' => now()->toISOString(),
            ]));

            $this->info('✓ Evento de WebSocket disparado correctamente');

            return 0;

        } catch (\Exception $e) {
            $this->error('Error al registrar la asistencia: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
