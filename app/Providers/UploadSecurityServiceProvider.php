<?php

namespace App\Providers;

use App\Configuration\UploadLimits;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider para la seguridad de cargas de archivos.
 * 
 * Este proveedor verifica la configuración de PHP al arrancar la aplicación
 * y registra advertencias si los límites no son seguros.
 */
class UploadSecurityServiceProvider extends ServiceProvider
{
    /**
     * Registra cualquier servicio de la aplicación.
     */
    public function register(): void
    {
        // Registrar singleton de UploadLimits para acceso global
        $this->app->singleton('upload.limits', function ($app) {
            return new class {
                public function getImportLimit(string $format = 'MB'): int|float
                {
                    return UploadLimits::getImportLimit($format);
                }

                public function getDocumentLimit(string $format = 'MB'): int|float
                {
                    return UploadLimits::getDocumentLimit($format);
                }

                public function getImageLimit(string $format = 'MB'): int|float
                {
                    return UploadLimits::getImageLimit($format);
                }

                public function formatBytes(int $bytes, int $decimals = 2): string
                {
                    return UploadLimits::formatBytes($bytes, $decimals);
                }

                public function isWithinLimit(int $sizeInBytes, int $limitInBytes): bool
                {
                    return UploadLimits::isWithinLimit($sizeInBytes, $limitInBytes);
                }

                public function isPhpConfigSafe(): array
                {
                    return UploadLimits::isPhpConfigSafe();
                }
            };
        });
    }

    /**
     * Realiza el bootstrap de cualquier servicio de la aplicación.
     */
    public function boot(): void
    {
        // Solo verificar en entorno de desarrollo y producción (no en testing)
        if (! $this->app->runningInConsole() || $this->app->environment('testing')) {
            return;
        }

        try {
            $config = UploadLimits::isPhpConfigSafe();

            if (!$config['is_safe']) {
                Log::warning('⚠️  Configuración de PHP insegura para cargas de archivos', [
                    'issues' => $config['issues'],
                    'current' => $config['current'],
                    'recommended' => $config['recommended'],
                ]);

                if ($this->app->environment('local', 'development')) {
                    // En desarrollo, mostrar advertencias más visibles
                    foreach ($config['issues'] as $issue) {
                        error_log("⚠️  UPLOAD SECURITY WARNING: {$issue}");
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error al verificar la configuración de límites de carga', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}

