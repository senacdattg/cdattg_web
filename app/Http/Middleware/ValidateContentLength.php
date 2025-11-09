<?php

namespace App\Http\Middleware;

use App\Configuration\UploadLimits;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para validar el Content-Length de las peticiones HTTP.
 * Previene ataques de denegación de servicio mediante archivos excesivamente grandes.
 */
class ValidateContentLength
{

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ?int $maxSize = null): Response
    {
        // Usar el tamaño personalizado si se proporciona, sino usar el por defecto
        $maxContentLength = $maxSize ?? UploadLimits::IMPORT_CONTENT_LENGTH_BYTES;

        // Obtener el Content-Length del header
        $contentLength = $request->header('Content-Length');

        // Si no existe el header Content-Length, rechazar la petición
        if ($contentLength === null && $request->isMethod('POST')) {
            return response()->json([
                'error' => 'Content-Length header es requerido',
                'message' => 'La petición debe incluir el header Content-Length.'
            ], Response::HTTP_LENGTH_REQUIRED);
        }

        // Validar que el Content-Length no exceda el límite
        if ($contentLength !== null && (int) $contentLength > $maxContentLength) {
            $maxSizeMB = round($maxContentLength / 1024 / 1024, 2);
            $requestSizeMB = round((int) $contentLength / 1024 / 1024, 2);

            return response()->json([
                'error' => 'Payload demasiado grande',
                'message' => sprintf(
                    'El tamaño de la petición (%sMB) excede el límite permitido de %sMB.',
                    $requestSizeMB,
                    $maxSizeMB
                ),
                'max_size_bytes' => $maxContentLength,
                'request_size_bytes' => (int) $contentLength
            ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE);
        }

        // Validar que el Content-Length sea un número válido
        if ($contentLength !== null && !is_numeric($contentLength)) {
            return response()->json([
                'error' => 'Content-Length inválido',
                'message' => 'El valor del header Content-Length debe ser un número.'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Validar que el Content-Length no sea negativo
        if ($contentLength !== null && (int) $contentLength < 0) {
            return response()->json([
                'error' => 'Content-Length inválido',
                'message' => 'El valor del header Content-Length no puede ser negativo.'
            ], Response::HTTP_BAD_REQUEST);
        }

        return $next($request);
    }
}
