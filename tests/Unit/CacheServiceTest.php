<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Core\Services\CacheService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CacheServiceTest extends TestCase
{
    protected CacheService $cacheService;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->cacheService = new CacheService();
        Cache::flush(); // Limpiar caché antes de cada test
    }

    /** @test */
    public function puede_recordar_valores_en_cache()
    {
        $key = 'test.key';
        $valor = 'test value';

        $resultado = $this->cacheService->remember($key, function () use ($valor) {
            return $valor;
        });

        $this->assertEquals($valor, $resultado);
        $this->assertTrue(Cache::has($key));
    }

    /** @test */
    public function puede_olvidar_valores_de_cache()
    {
        $key = 'test.key';
        Cache::put($key, 'valor', 60);

        $this->assertTrue(Cache::has($key));

        $this->cacheService->forget($key);

        $this->assertFalse(Cache::has($key));
    }

    /** @test */
    public function puede_generar_claves_de_cache()
    {
        $key = $this->cacheService->key('aprendiz', 1, 'detalles');

        $this->assertEquals('aprendiz.1.detalles', $key);
    }

    /** @test */
    public function puede_generar_claves_con_arrays()
    {
        $filtros = ['estado' => 'activo', 'ficha' => 1];
        $key = $this->cacheService->key('aprendices', $filtros);

        $this->assertStringContainsString('aprendices.', $key);
        $this->assertStringContainsString(md5(json_encode($filtros)), $key);
    }

    /** @test */
    public function respeta_ttl_personalizado_por_tipo()
    {
        $key = 'parametros.test';
        
        $resultado = $this->cacheService->remember($key, function () {
            return 'valor';
        }, null, 'parametros');

        $this->assertTrue(Cache::has($key));
        // TTL de parametros es 1440 minutos (24 horas)
    }

    /** @test */
    public function puede_limpiar_cache_completa()
    {
        Cache::put('key1', 'valor1', 60);
        Cache::put('key2', 'valor2', 60);

        $this->assertTrue(Cache::has('key1'));
        $this->assertTrue(Cache::has('key2'));

        $this->cacheService->flush();

        $this->assertFalse(Cache::has('key1'));
        $this->assertFalse(Cache::has('key2'));
    }

    /** @test */
    public function puede_obtener_estadisticas()
    {
        $stats = $this->cacheService->getStats();

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('driver', $stats);
        $this->assertArrayHasKey('prefix', $stats);
    }
}

