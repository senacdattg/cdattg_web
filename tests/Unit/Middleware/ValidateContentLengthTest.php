<?php

namespace Tests\Unit\Middleware;

use App\Configuration\UploadLimits;
use App\Http\Middleware\ValidateContentLength;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class ValidateContentLengthTest extends TestCase
{
    private ValidateContentLength $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new ValidateContentLength();
    }

    public function test_permite_peticiones_con_content_length_valido(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('Content-Length', '1024'); // 1KB

        $response = $this->middleware->handle(
            $request,
            function ($req) {
                return response()->json(['success' => true]);
            }
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_rechaza_peticiones_con_content_length_excesivo(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('Content-Length', (string) (UploadLimits::IMPORT_CONTENT_LENGTH_BYTES + 1));

        $response = $this->middleware->handle(
            $request,
            function ($req) {
                return response()->json(['success' => true]);
            }
        );

        $this->assertEquals(Response::HTTP_REQUEST_ENTITY_TOO_LARGE, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Payload demasiado grande', $data['error']);
    }

    public function test_rechaza_peticiones_post_sin_content_length(): void
    {
        $request = Request::create('/test', 'POST');

        $response = $this->middleware->handle(
            $request,
            function ($req) {
                return response()->json(['success' => true]);
            }
        );

        $this->assertEquals(Response::HTTP_LENGTH_REQUIRED, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Content-Length header es requerido', $data['error']);
    }

    public function test_rechaza_content_length_negativo(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('Content-Length', '-1');

        $response = $this->middleware->handle(
            $request,
            function ($req) {
                return response()->json(['success' => true]);
            }
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Content-Length inválido', $data['error']);
    }

    public function test_rechaza_content_length_no_numerico(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('Content-Length', 'invalid');

        $response = $this->middleware->handle(
            $request,
            function ($req) {
                return response()->json(['success' => true]);
            }
        );

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('Content-Length inválido', $data['error']);
    }

    public function test_permite_especificar_limite_personalizado(): void
    {
        $customLimit = 5000; // 5KB
        $request = Request::create('/test', 'POST');
        $request->headers->set('Content-Length', '10000'); // 10KB

        $response = $this->middleware->handle(
            $request,
            function ($req) {
                return response()->json(['success' => true]);
            },
            $customLimit
        );

        $this->assertEquals(Response::HTTP_REQUEST_ENTITY_TOO_LARGE, $response->getStatusCode());
    }

    public function test_permite_peticiones_get_sin_content_length(): void
    {
        $request = Request::create('/test', 'GET');

        $response = $this->middleware->handle(
            $request,
            function ($req) {
                return response()->json(['success' => true]);
            }
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_permite_content_length_cero(): void
    {
        $request = Request::create('/test', 'POST');
        $request->headers->set('Content-Length', '0');

        $response = $this->middleware->handle(
            $request,
            function ($req) {
                return response()->json(['success' => true]);
            }
        );

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_respuesta_incluye_informacion_detallada_del_error(): void
    {
        $request = Request::create('/test', 'POST');
        $contentLength = UploadLimits::IMPORT_CONTENT_LENGTH_BYTES + 1000000; // +1MB sobre el límite
        $request->headers->set('Content-Length', (string) $contentLength);

        $response = $this->middleware->handle(
            $request,
            function ($req) {
                return response()->json(['success' => true]);
            }
        );

        $data = json_decode($response->getContent(), true);
        
        $this->assertArrayHasKey('max_size_bytes', $data);
        $this->assertArrayHasKey('request_size_bytes', $data);
        $this->assertEquals(UploadLimits::IMPORT_CONTENT_LENGTH_BYTES, $data['max_size_bytes']);
        $this->assertEquals($contentLength, $data['request_size_bytes']);
    }
}

