<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\QrScanned;
use App\Events\AsistenciaCreated;

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
        // Crear una asistencia de prueba
        $asistencia = new \App\Models\AsistenciaAprendiz([
            'instructor_ficha_id' => 1,
            'aprendiz_ficha_id' => 1,
            'hora_ingreso' => now()->format('H:i:s'),
            'hora_salida' => null,
        ]);

        event(new AsistenciaCreated($asistencia));
        $this->info('Evento AsistenciaCreated enviado con datos de prueba');
    }
}
