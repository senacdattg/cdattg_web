<?php

namespace App\Providers;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Pagination\Paginator as PaginationPaginator;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use App\Models\AsistenciaAprendiz;
use App\Observers\AsistenciaAprendizObserver;
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

        // Registrar driver de Google Drive
        try {
            Storage::extend('google', function ($app, $config) {
                $options = [];

                if (!empty($config['teamDriveId'] ?? null)) {
                    $options['teamDriveId'] = $config['teamDriveId'];
                }

                $client = new \Google\Client();
                $client->setClientId($config['clientId']);
                $client->setClientSecret($config['clientSecret']);
                $client->refreshToken($config['refreshToken']);
                
                $service = new \Google\Service\Drive($client);
                $adapter = new \Masbug\Flysystem\GoogleDriveAdapter($service, $config['folderId'] ?? '/', $options);
                $driver = new \League\Flysystem\Filesystem($adapter);

                return new \Illuminate\Filesystem\FilesystemAdapter($driver, $adapter);
            });
        } catch (\Exception $e) {
            \Log::error('Error al registrar driver de Google Drive: ' . $e->getMessage());
        }
    }
}
