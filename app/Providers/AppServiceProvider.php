<?php

namespace App\Providers;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\Paginator as PaginationPaginator;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use App\Models\AsistenciaAprendiz;
use App\Models\Aprendiz;
use App\Observers\AsistenciaAprendizObserver;
use App\Observers\AprendizObserver;
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

        // if ($this->app->environment('production')) {
        //     URL::forceScheme('https');
        // }

        // Registrar observadores
        AsistenciaAprendiz::observe(AsistenciaAprendizObserver::class);
        Aprendiz::observe(AprendizObserver::class);
    }
}
