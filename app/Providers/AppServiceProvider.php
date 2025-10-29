<?php

namespace App\Providers;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\Paginator as PaginationPaginator;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\AsistenciaAprendiz;
use App\Models\Aprendiz;
use App\Models\Instructor;
use App\Observers\AsistenciaAprendizObserver;
use App\Observers\AprendizObserver;
use App\Observers\InstructorObserver;
// \Illuminate\Support\Facades\URL::forceScheme('https');

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        setlocale(LC_ALL, 'es_ES', 'es', 'ES', 'es_ES.utf8');
        \Carbon\Carbon::setLocale('es');
        date_default_timezone_set('America/Bogota');
        Schema::defaultStringLength(191);
        PaginationPaginator::useBootstrap();

        // Configurar reglas de pluralización para evitar problemas con tablas
        $this->configureInflector();

        // if ($this->app->environment('production')) {
        //     URL::forceScheme('https');
        // }

        // Registrar observadores
        AsistenciaAprendiz::observe(AsistenciaAprendizObserver::class);
        Aprendiz::observe(AprendizObserver::class);
        Instructor::observe(InstructorObserver::class);
    }

    /**
     * Configurar reglas de pluralización personalizadas
     */
    protected function configureInflector()
    {
        // Registrar reglas de pluralización para evitar que Laravel
        // pluralice 'instructor' como 'instructores' en español
        \Illuminate\Support\Str::macro('plural', function ($value, $count = 2) {
            if ($value === 'instructor') {
                return $count === 1 ? 'instructor' : 'instructors';
            }
            return \Illuminate\Support\Str::plural($value, $count);
        });
    }
}
