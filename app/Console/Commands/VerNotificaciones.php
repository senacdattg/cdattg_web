<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventario\Notificacion;

class VerNotificaciones extends Command
{
    protected $signature = 'ver:notificaciones';
    protected $description = 'Ver las últimas notificaciones en la base de datos';

    public function handle()
    {
        $this->info('=== NOTIFICACIONES EN LA BASE DE DATOS ===');
        $this->info('Total: ' . Notificacion::count());
        $this->newLine();

        $notificaciones = Notificacion::latest()->take(10)->get();

        foreach ($notificaciones as $notif) {
            $tipo = basename(str_replace('\\', '/', $notif->tipo));
            $this->line("ID: {$notif->id}");
            $this->line("Tipo: {$tipo}");
            $this->line("Usuario: {$notif->notifiable_id}");
            $this->line("Leída: " . ($notif->leida_en ? 'Sí' : 'No'));
            $this->line("Fecha: {$notif->created_at}");
            
            // Decodificar datos
            $datos = is_string($notif->datos) ? json_decode($notif->datos, true) : $notif->datos;
            if (isset($datos['tipo'])) {
                $this->line("Subtipo: {$datos['tipo']}");
            }
            if (isset($datos['producto'])) {
                $producto = is_array($datos['producto']) ? $datos['producto']['producto'] ?? 'N/A' : $datos['producto'];
                $this->line("Producto: {$producto}");
            }
            
            $this->newLine();
        }

        return 0;
    }
}
