<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class ModuleRouteServiceProvider extends ServiceProvider
{
    /**
     * Mapeo de módulos y sus configuraciones
     */
    protected array $modules = [
        'aprendices' => [
            'prefix' => 'aprendices',
            'middleware' => ['auth', 'can:VER APRENDIZ'],
            'namespace' => 'App\Http\Controllers',
        ],
        'instructores' => [
            'prefix' => 'instructores',
            'middleware' => ['auth'],
            'namespace' => 'App\Http\Controllers',
        ],
        'asistencias' => [
            'prefix' => 'asistencias',
            'middleware' => ['auth'],
            'namespace' => 'App\Http\Controllers',
        ],
        'caracterizacion' => [
            'prefix' => 'caracterizacion',
            'middleware' => ['auth'],
            'namespace' => 'App\Http\Controllers',
        ],
        'configuracion' => [
            'prefix' => 'configuracion',
            'middleware' => ['auth', 'can:ADMINISTRAR SISTEMA'],
            'namespace' => 'App\Http\Controllers',
        ],
        'reportes' => [
            'prefix' => 'reportes',
            'middleware' => ['auth'],
            'namespace' => 'App\Http\Controllers',
        ],
    ];

    /**
     * Define las rutas del módulo
     *
     * @return void
     */
    public function boot(): void
    {
        parent::boot();
    }

    /**
     * Registra las rutas de los módulos
     *
     * @return void
     */
    public function map(): void
    {
        $this->mapApiRoutes();
        $this->mapWebRoutes();
        $this->mapModuleRoutes();
    }

    /**
     * Rutas API
     */
    protected function mapApiRoutes(): void
    {
        Route::prefix('api')
            ->middleware('api')
            ->group(base_path('routes/api.php'));
    }

    /**
     * Rutas Web principales
     */
    protected function mapWebRoutes(): void
    {
        Route::middleware('web')
            ->group(base_path('routes/web.php'));
    }

    /**
     * Rutas de módulos
     */
    protected function mapModuleRoutes(): void
    {
        foreach ($this->modules as $module => $config) {
            $routeFile = base_path("routes/modules/{$module}.php");
            
            if (file_exists($routeFile)) {
                Route::middleware(['web', ...$config['middleware']])
                    ->prefix($config['prefix'])
                    ->namespace($config['namespace'])
                    ->name("{$module}.")
                    ->group($routeFile);
            }
        }
    }

    /**
     * Registra una ruta de módulo personalizada
     *
     * @param string $module
     * @param array $config
     * @return void
     */
    public function registerModule(string $module, array $config): void
    {
        $this->modules[$module] = $config;
    }
}

