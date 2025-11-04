<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Inventario\Producto;
use App\Models\Inventario\Orden;
use App\Notifications\StockBajoNotification;
use App\Notifications\NuevaOrdenNotification;

class TestNotificaciones extends Command
{
    protected $signature = 'test:notificaciones';
    protected $description = 'Crear notificaciones de prueba para el sistema';

    public function handle()
    {
        $this->info('=== SISTEMA DE PRUEBA DE NOTIFICACIONES ===');
        $this->newLine();

        // 1. Obtener usuario SUPER ADMINISTRADOR
        $superadmin = User::role('SUPER ADMINISTRADOR')->first();

        if (!$superadmin) {
            $this->error('❌ No se encontró ningún usuario con rol SUPER ADMINISTRADOR');
            return 1;
        }

        $this->info("✅ Usuario encontrado: {$superadmin->name} ({$superadmin->email})");
        $this->info("   ID: {$superadmin->id}");
        $this->newLine();

        // 2. Verificar permiso
        if (!$superadmin->hasPermissionTo('VER NOTIFICACION')) {
            $this->warn('⚠️  El usuario NO tiene el permiso VER NOTIFICACION');
            $this->info('   Asignando permiso...');
            $superadmin->givePermissionTo('VER NOTIFICACION');
            $this->info('✅ Permiso asignado');
            $this->newLine();
        } else {
            $this->info('✅ El usuario tiene el permiso VER NOTIFICACION');
            $this->newLine();
        }

        // 3. Crear notificación de Stock Bajo
        $this->info('📦 Creando notificación de STOCK BAJO...');
        $producto = Producto::first();

        if ($producto) {
            $superadmin->notify(new StockBajoNotification($producto, 5, 10));
            $this->info('✅ Notificación de Stock Bajo enviada');
            $this->info("   Producto: {$producto->producto}");
            $this->info('   Stock actual: 5 unidades');
        } else {
            $this->warn('⚠️  No hay productos en la base de datos');
        }
        $this->newLine();

        // 4. Crear notificación de Nueva Orden
        $this->info('📋 Creando notificación de NUEVA ORDEN...');
        $orden = Orden::with(['detalles', 'userCreate'])->first();

        if ($orden) {
            $superadmin->notify(new NuevaOrdenNotification($orden));
            $this->info('✅ Notificación de Nueva Orden enviada');
            $this->info("   Orden ID: {$orden->id}");
        } else {
            $this->warn('⚠️  No hay órdenes en la base de datos');
        }
        $this->newLine();

        // 5. Resumen
        $notificacionesCount = $superadmin->notifications()->count();
        $noLeidasCount = $superadmin->unreadNotifications()->count();

        $this->info('=== RESUMEN ===');
        $this->info("📬 Total de notificaciones: {$notificacionesCount}");
        $this->info("🔔 Notificaciones no leídas: {$noLeidasCount}");
        $this->newLine();

        // 6. Últimas notificaciones
        $this->info('=== ÚLTIMAS NOTIFICACIONES ===');
        $ultimasNotificaciones = $superadmin->notifications()->take(5)->get();

        foreach ($ultimasNotificaciones as $index => $notif) {
            $numero = $index + 1;
            $tipo = class_basename($notif->tipo);
            $leida = $notif->leida_en ? '✅ Leída' : '🔔 No leída';
            $fecha = $notif->created_at->diffForHumans();
            
            $this->line("{$numero}. {$tipo} - {$leida} ({$fecha})");
        }

        $this->newLine();
        $this->info('=== PRUEBA COMPLETADA ===');
        $this->info('Ahora puedes:');
        $this->info('1. Visitar: http://localhost/inventario/notificaciones');
        $this->info("2. Iniciar sesión con: {$superadmin->email}");
        $this->info('3. Verificar que las notificaciones aparezcan');

        return 0;
    }
}
