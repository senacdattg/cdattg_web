<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrdenAprobadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $detalleOrden;
    public $aprobador;

    /**
     * Create a new notification instance.
     */
    public function __construct($detalleOrden, $aprobador)
    {
        $this->detalleOrden = $detalleOrden;
        $this->aprobador = $aprobador;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $orden = $this->detalleOrden->orden;
        $producto = $this->detalleOrden->producto;
        $tipoOrden = $orden->tipoOrden->parametro->name ?? 'N/A';
        
        return (new MailMessage)
            ->subject('✅ Tu Solicitud ha sido Aprobada - Orden #' . $orden->id)
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line('¡Buenas noticias! Tu solicitud de ' . strtolower($tipoOrden) . ' ha sido aprobada.')
            ->line('**Orden:** #' . $orden->id)
            ->line('**Tipo:** ' . $tipoOrden)
            ->line('**Producto:** ' . $producto->producto)
            ->line('**Cantidad Aprobada:** ' . $this->detalleOrden->cantidad . ' unidades')
            ->line('**Aprobado por:** ' . $this->aprobador->name)
            ->when($orden->fecha_devolucion, function ($message) use ($orden) {
                return $message->line('**Fecha de Devolución:** ' . $orden->fecha_devolucion->format('d/m/Y'))
                    ->line('⚠️ Recuerda devolver el producto en la fecha indicada.');
            })
            ->action('Ver Detalles', url('/inventario/ordenes/' . $orden->id))
            ->line('Puedes pasar a recoger el producto.')
            ->salutation('Saludos, ' . config('app.name'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'orden_id' => $this->detalleOrden->orden->id,
            'detalle_orden_id' => $this->detalleOrden->id,
            'producto' => $this->detalleOrden->producto->producto,
            'cantidad' => $this->detalleOrden->cantidad,
            'aprobador' => $this->aprobador->name,
            'tipo' => 'orden_aprobada',
        ];
    }
}
