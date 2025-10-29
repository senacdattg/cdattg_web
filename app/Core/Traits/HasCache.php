<?php

namespace App\Core\Traits;

use App\Core\Services\CacheService;

trait HasCache
{
    /**
     * Instancia del servicio de caché
     *
     * @var CacheService
     */
    protected $cacheService;

    /**
     * Prefijo de caché para esta clase
     *
     * @var string
     */
    protected $cachePrefix;

    /**
     * Tipo de caché para TTL personalizado
     *
     * @var string|null
     */
    protected $cacheType;

    /**
     * Tags de caché
     *
     * @var array
     */
    protected $cacheTags = [];

    /**
     * Inicializa el servicio de caché
     *
     * @return void
     */
    protected function initializeCache(): void
    {
        if (!$this->cacheService) {
            $this->cacheService = app(CacheService::class);
        }

        if (!$this->cachePrefix) {
            // Generar prefijo automático basado en el nombre de la clase
            $className = class_basename($this);
            $this->cachePrefix = strtolower(str_replace(['Repository', 'Service'], '', $className));
        }
    }

    /**
     * Recuerda un valor en caché
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    protected function cache(string $key, callable $callback, ?int $ttl = null)
    {
        $this->initializeCache();
        
        $fullKey = $this->cacheService->key($this->cachePrefix, $key);
        
        return $this->cacheService->remember($fullKey, $callback, $ttl, $this->cacheType);
    }

    /**
     * Recuerda un valor en caché con tags
     *
     * @param string $key
     * @param callable $callback
     * @param int|null $ttl
     * @return mixed
     */
    protected function cacheWithTags(string $key, callable $callback, ?int $ttl = null)
    {
        $this->initializeCache();
        
        $fullKey = $this->cacheService->key($this->cachePrefix, $key);
        $tags = array_merge([$this->cachePrefix], $this->cacheTags);
        
        return $this->cacheService->rememberWithTags($tags, $fullKey, $callback, $ttl);
    }

    /**
     * Olvida un valor de caché
     *
     * @param string $key
     * @return bool
     */
    protected function forgetCache(string $key): bool
    {
        $this->initializeCache();
        
        $fullKey = $this->cacheService->key($this->cachePrefix, $key);
        
        return $this->cacheService->forget($fullKey);
    }

    /**
     * Limpia toda la caché de esta clase
     *
     * @return bool
     */
    protected function flushCache(): bool
    {
        $this->initializeCache();
        
        $tags = array_merge([$this->cachePrefix], $this->cacheTags);
        
        return $this->cacheService->flushTags($tags);
    }

    /**
     * Genera clave de caché
     *
     * @param mixed ...$parts
     * @return string
     */
    protected function cacheKey(...$parts): string
    {
        $this->initializeCache();
        
        return $this->cacheService->key($this->cachePrefix, ...$parts);
    }
}

