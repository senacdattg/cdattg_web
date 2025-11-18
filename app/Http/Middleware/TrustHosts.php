<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustHosts as Middleware;

class TrustHosts extends Middleware
{
    /**
     * Get the host patterns that should be trusted.
     *
     * @return array<int, string|null>
     */
    public function hosts(): array
    {
        $hosts = [
            // Permitir dominios de ngrok
            '*.ngrok-free.app',
            '*.ngrok.io',
            '*.ngrok.app',
        ];

        // Solo agregar subdominios si APP_URL está configurado correctamente
        try {
            $appUrl = config('app.url');
            if ($appUrl && $appUrl !== 'http://localhost') {
                $hosts[] = $this->allSubdomainsOfApplicationUrl();
            }
        } catch (\Exception $e) {
            // Si hay error al obtener subdominios, continuar sin ellos
            // Esto previene errores cuando APP_URL no está configurado
        }

        return $hosts;
    }
}
