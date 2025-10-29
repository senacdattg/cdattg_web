<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\QrScanned;
use App\Events\NuevaAsistenciaRegistrada;

class TestWebSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:test {type=qr}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba los eventos de WebSocket';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $type = $this->argument('type');

        if ($type === 'qr') {
            $this->testQrEvent();
        } elseif ($type === 'asistencia') {
            $this->testAsistenciaEvent();
        } else {
            $this->error('Tipo no válido. Use "qr" o "asistencia"');
            return 1;
        }

        $this->info('Evento de prueba enviado correctamente');
        return 0;
    }

    private function testQrEvent()
    {
        $qrData = [
            'numero_documento' => '12345678',
            'aprendiz_nombre' => 'Juan Pérez',
            'ficha_id' => 1,
            'hora_ingreso' => now()->format('H:i:s'),
            'tipo' => 'entrada',
            'instructor_id' => 1,
        ];

        event(new QrScanned($qrData));
        $this->info('Evento QrScanned enviado con datos de prueba');
    }

    private function testAsistenciaEvent()
    {
        // Crear datos de asistencia de prueba
        $asistenciaData = [
            'id' => 999,
            'aprendiz' => 'Juan Pérez - Prueba',
            'estado' => 'entrada',
            'timestamp' => now()->toISOString(),
        ];

        event(new NuevaAsistenciaRegistrada($asistenciaData));
        $this->info('Evento NuevaAsistenciaRegistrada enviado con datos de prueba');
    }
}
