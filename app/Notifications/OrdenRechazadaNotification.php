<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrdenRechazadaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $detalleOrden;
    public $aprobador;
    public $motivoRechazo;

    /**
     * Create a new notification instance.
     */
    public function __construct($detalleOrden, $aprobador, $motivoRechazo = null)
    {
        $this->detalleOrden = $detalleOrden;
        $this->aprobador = $aprobador;
        $this->motivoRechazo = $motivoRechazo;
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
        
        $message = (new MailMessage)
            ->subject('❌ Tu Solicitud ha sido Rechazada - Orden #' . $orden->id)
            ->greeting('Hola, ' . $notifiable->name)
            ->line('Lamentamos informarte que tu solicitud de ' . strtolower($tipoOrden) . ' ha sido rechazada.')
            ->line('**Orden:** #' . $orden->id)
            ->line('**Tipo:** ' . $tipoOrden)
            ->line('**Producto:** ' . $producto->producto)
            ->line('**Cantidad Solicitada:** ' . $this->detalleOrden->cantidad . ' unidades')
            ->line('**Rechazado por:** ' . $this->aprobador->name);
        
        if ($this->motivoRechazo) {
            $message->line('**Motivo del Rechazo:**')
                ->line($this->motivoRechazo);
        }
        
        $message->action('Ver Detalles', url('/inventario/ordenes/' . $orden->id))
            ->line('Si tienes alguna pregunta, por favor contacta al administrador.')
            ->salutation('Saludos, ' . config('app.name'));
        
        return $message;
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
            'producto' => [
                'id' => $this->detalleOrden->producto->id,
                'producto' => $this->detalleOrden->producto->producto,
            ],
            'cantidad' => $this->detalleOrden->cantidad,
            'aprobador' => [
                'id' => $this->aprobador->id,
                'name' => $this->aprobador->name,
            ],
            'motivo_rechazo' => $this->motivoRechazo,
            'tipo' => 'orden_rechazada',
        ];
    }
}
