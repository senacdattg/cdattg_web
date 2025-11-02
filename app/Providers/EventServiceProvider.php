<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Models\Inventario\Producto;
use App\Models\Inventario\Orden;
use App\Observers\Inventario\ProductoObserver;
use App\Observers\Inventario\OrdenObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        \App\Events\Inventario\ProductoBajoStock::class => [
            \App\Listeners\Inventario\EnviarNotificacionProductoBajoStock::class,
        ],
        \App\Events\Inventario\NuevaOrdenCreada::class => [
            \App\Listeners\Inventario\EnviarNotificacionNuevaOrden::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Producto::observe(ProductoObserver::class);
        Orden::observe(OrdenObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
