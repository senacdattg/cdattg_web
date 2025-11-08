<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockBajoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $producto;
    public $stockActual;
    public $stockMinimo;

    /**
     * Create a new notification instance.
     */
    public function __construct($producto, $stockActual, $stockMinimo = 10)
    {
        $this->producto = $producto;
        $this->stockActual = $stockActual;
        $this->stockMinimo = $stockMinimo;
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
        $nivelAlerta = $this->stockActual == 0 ? 'CRÍTICO' : 'BAJO';

        return (new MailMessage)
            ->subject('⚠️ Alerta de Stock ' . $nivelAlerta . ' - ' . $this->producto->producto)
            ->greeting('¡Hola, ' . $notifiable->name . '!')
            ->line('Se ha detectado un nivel de stock ' . strtolower($nivelAlerta) . ' para el siguiente producto:')
            ->line('**Producto:** ' . $this->producto->producto)
            ->line('**Código:** ' . ($this->producto->codigo_barras ?? 'N/A'))
            ->line('**Stock Actual:** ' . $this->stockActual . ' unidades')
            ->line('**Stock Mínimo:** ' . $this->stockMinimo . ' unidades')
            ->when($this->stockActual == 0, function ($message) {
                return $message->line('🚨 **ATENCIÓN:** El producto está agotado y requiere reabastecimiento inmediato.');
            })
            ->action('Ver Inventario', url('/inventario/productos'))
            ->line('Por favor, considera reabastecer este producto lo antes posible.')
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
            'producto_id' => $this->producto->id,
            'producto_nombre' => $this->producto->producto,
            'stock_actual' => $this->stockActual,
            'stock_minimo' => $this->stockMinimo,
            'tipo' => 'stock_bajo',
            'nivel_alerta' => $this->stockActual == 0 ? 'critico' : 'bajo',
        ];
    }
}
