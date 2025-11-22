<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        \App\Models\RedConocimiento::class => \App\Policies\RedConocimientoPolicy::class,
        \App\Models\Aprendiz::class => \App\Policies\AprendizPolicy::class,
        \App\Models\ProgramaFormacion::class => \App\Policies\ProgramaFormacionPolicy::class,
        \App\Models\FichaCaracterizacion::class => \App\Policies\FichaCaracterizacionPolicy::class,
        \App\Models\Persona::class => \App\Policies\PersonaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
      
    }
}
