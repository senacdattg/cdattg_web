<?php

namespace App\Services;

use App\Models\User;
use App\Models\Instructor;
use App\Models\Aprendiz;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Collection;

class NotificacionService
{
    /**
     * Notifica a instructor sobre nueva ficha asignada
     *
     * @param Instructor $instructor
     * @param array $datosFicha
     * @return bool
     */
    public function notificarNuevaFichaInstructor(Instructor $instructor, array $datosFicha): bool
    {
        try {
            $user = $instructor->persona->user ?? null;

            if (!$user || !$user->email) {
                Log::warning('Instructor sin email para notificación', [
                    'instructor_id' => $instructor->id,
                ]);
                return false;
            }

            // En producción enviar email real
            // Mail::to($user->email)->send(new NuevaFichaAsignada($instructor, $datosFicha));

            Log::info('Notificación de nueva ficha enviada', [
                'instructor_id' => $instructor->id,
                'email' => $user->email,
                'ficha' => $datosFicha['numero'] ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error enviando notificación', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Notifica a aprendices sobre cambio en su ficha
     *
     * @param Collection $aprendices
     * @param string $mensaje
     * @return int Número de notificaciones enviadas
     */
    public function notificarAprendices(Collection $aprendices, string $mensaje): int
    {
        $enviados = 0;

        foreach ($aprendices as $aprendiz) {
            try {
                $persona = $aprendiz->persona;
                
                if (!$persona || !$persona->email) {
                    continue;
                }

                // En producción enviar email real
                // Mail::to($persona->email)->send(new NotificacionAprendiz($mensaje));

                $enviados++;

                Log::info('Notificación enviada a aprendiz', [
                    'aprendiz_id' => $aprendiz->id,
                    'email' => $persona->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Error enviando notificación a aprendiz', [
                    'aprendiz_id' => $aprendiz->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Log::info('Notificaciones masivas enviadas', [
            'total_aprendices' => $aprendices->count(),
            'enviados' => $enviados,
        ]);

        return $enviados;
    }

    /**
     * Notifica sobre asistencia registrada
     *
     * @param array $datosAsistencia
     * @return bool
     */
    public function notificarAsistenciaRegistrada(array $datosAsistencia): bool
    {
        try {
            // Broadcast en tiempo real usando WebSockets
            broadcast(new \App\Events\NuevaAsistenciaRegistrada($datosAsistencia))->toOthers();

            Log::info('Notificación de asistencia broadcast', [
                'aprendiz' => $datosAsistencia['aprendiz'] ?? 'N/A',
                'estado' => $datosAsistencia['estado'] ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error en broadcast de asistencia', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Notifica recordatorio de clase
     *
     * @param Instructor $instructor
     * @param array $datosClase
     * @return bool
     */
    public function notificarRecordatorioClase(Instructor $instructor, array $datosClase): bool
    {
        try {
            $user = $instructor->persona->user ?? null;

            if (!$user || !$user->email) {
                return false;
            }

            // En producción enviar email real
            // Mail::to($user->email)->send(new RecordatorioClase($datosClase));

            Log::info('Recordatorio de clase enviado', [
                'instructor_id' => $instructor->id,
                'clase' => $datosClase['ficha'] ?? 'N/A',
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error enviando recordatorio', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Notifica a administradores sobre evento importante
     *
     * @param string $tipo
     * @param array $datos
     * @return int
     */
    public function notificarAdministradores(string $tipo, array $datos): int
    {
        try {
            $administradores = User::role('ADMINISTRADOR')->get();
            $enviados = 0;

            foreach ($administradores as $admin) {
                if ($admin->email) {
                    // En producción enviar email real
                    // Mail::to($admin->email)->send(new NotificacionAdmin($tipo, $datos));
                    $enviados++;
                }
            }

            Log::info('Notificaciones a administradores enviadas', [
                'tipo' => $tipo,
                'enviados' => $enviados,
            ]);

            return $enviados;
        } catch (\Exception $e) {
            Log::error('Error notificando administradores', [
                'tipo' => $tipo,
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    /**
     * Programa notificación para envío posterior
     *
     * @param User $user
     * @param string $mensaje
     * @param Carbon $cuando
     * @return bool
     */
    public function programarNotificacion(User $user, string $mensaje, Carbon $cuando): bool
    {
        try {
            // Usar Jobs con delay
            // dispatch(new EnviarNotificacion($user, $mensaje))->delay($cuando);

            Log::info('Notificación programada', [
                'user_id' => $user->id,
                'cuando' => $cuando->toDateTimeString(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error programando notificación', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}

