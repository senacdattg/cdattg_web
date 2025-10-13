<?php

namespace App\Core\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CacheService
{
    /**
     * Tiempo de caché por defecto en minutos
     */
    const DEFAULT_TTL = 60; // 1 hora

    /**
     * Tiempos de caché personalizados por tipo
     */
    const TTL_CONFIG = [
        'parametros' => 1440,      // 24 horas
        'temas' => 1440,           // 24 horas
        'regionales' => 720,       // 12 horas
        'programas' => 360,        // 6 horas
        'fichas' => 60,            // 1 hora
        'aprendices' => 30,        // 30 minutos
        'instructores' => 30,      // 30 minutos
        'asistencias' => 5,        // 5 minutos
        'estadisticas' => 15,      // 15 minutos
    ];

    /**
     * Obtiene datos de caché o ejecuta el callback
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl Tiempo en minutos
     * @param string|null $tipo
     * @return mixed
     */
    public function remember(string $key, callable $callback, ?int $ttl = null, ?string $tipo = null)
    {
        $ttl = $ttl ?? ($tipo ? self::TTL_CONFIG[$tipo] ?? self::DEFAULT_TTL : self::DEFAULT_TTL);

        return Cache::remember($key, now()->addMinutes($ttl), function () use ($callback, $key) {
            if (app()->environment('local')) {
                Log::debug("Cache MISS: {$key}");
            }
            return $callback();
        });
    }

    /**
     * Obtiene datos de caché o ejecuta el callback con tags
     *
     * @param array $tags
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    public function rememberWithTags(array $tags, string $key, callable $callback, ?int $ttl = null)
    {
        $ttl = $ttl ?? self::DEFAULT_TTL;

        // Redis/Memcached soportan tags, file cache no
        if (config('cache.default') === 'file') {
            return $this->remember($key, $callback, $ttl);
        }

        return Cache::tags($tags)->remember($key, now()->addMinutes($ttl), $callback);
    }

    /**
     * Invalida caché por clave
     *
     * @param string $key
     * @return bool
     */
    public function forget(string $key): bool
    {
        if (app()->environment('local')) {
            Log::debug("Cache FORGET: {$key}");
        }
        return Cache::forget($key);
    }

    /**
     * Invalida caché por tags
     *
     * @param array $tags
     * @return bool
     */
    public function flushTags(array $tags): bool
    {
        if (config('cache.default') === 'file') {
            // Flush manual para file cache
            foreach ($tags as $tag) {
                Cache::forget($tag . '.*');
            }
            return true;
        }

        if (app()->environment('local')) {
            Log::debug("Cache FLUSH TAGS: " . implode(', ', $tags));
        }

        return Cache::tags($tags)->flush();
    }

    /**
     * Limpia toda la caché
     *
     * @return bool
     */
    public function flush(): bool
    {
        Log::info('Cache: Limpieza completa de caché');
        return Cache::flush();
    }

    /**
     * Genera clave de caché con prefijo
     *
     * @param string $prefix
     * @param mixed ...$parts
     * @return string
     */
    public function key(string $prefix, ...$parts): string
    {
        $key = $prefix;
        foreach ($parts as $part) {
            if (is_array($part)) {
                $key .= '.' . md5(json_encode($part));
            } else {
                $key .= '.' . $part;
            }
        }
        return $key;
    }

    /**
     * Obtiene estadísticas de caché
     *
     * @return array
     */
    public function getStats(): array
    {
        // Implementación básica, puede extenderse según el driver
        return [
            'driver' => config('cache.default'),
            'prefix' => config('cache.prefix'),
        ];
    }

    /**
     * Pre-carga datos frecuentes
     *
     * @return void
     */
    public function warmup(): void
    {
        Log::info('Cache: Iniciando precarga de datos frecuentes');

        // Precargar parámetros del sistema
        $this->remember('parametros.sistema', function () {
            return \App\Models\Parametro::where('status', true)->get();
        }, null, 'parametros');

        // Precargar regionales activas
        $this->remember('regionales.activas', function () {
            return \App\Models\Regional::where('status', true)->get();
        }, null, 'regionales');

        // Precargar temas y parámetros
        $this->remember('temas.todos', function () {
            return \App\Models\Tema::with(['parametros' => function ($q) {
                $q->wherePivot('status', 1);
            }])->get();
        }, null, 'temas');

        Log::info('Cache: Precarga completada');
    }
}

