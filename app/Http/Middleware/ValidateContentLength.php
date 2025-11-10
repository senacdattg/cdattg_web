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
        $maxContentLength = $maxSize ?? UploadLimits::GENERAL_CONTENT_LENGTH_BYTES;

        // Obtener el Content-Length del header
        $contentLength = $request->header('Content-Length');
        $error = null;

        if ($contentLength === null && $request->isMethod('POST')) {
            $error = [
                'status' => Response::HTTP_LENGTH_REQUIRED,
                'payload' => [
                    'error' => 'Content-Length header es requerido',
                    'message' => 'La petición debe incluir el header Content-Length.',
                ],
            ];
        } elseif ($contentLength !== null && !is_numeric($contentLength)) {
            $error = [
                'status' => Response::HTTP_BAD_REQUEST,
                'payload' => [
                    'error' => 'Content-Length inválido',
                    'message' => 'El valor del header Content-Length debe ser un número.',
                ],
            ];
        } elseif ($contentLength !== null) {
            $contentLengthValue = (int) $contentLength;

            if ($contentLengthValue < 0) {
                $error = [
                    'status' => Response::HTTP_BAD_REQUEST,
                    'payload' => [
                        'error' => 'Content-Length inválido',
                        'message' => 'El valor del header Content-Length no puede ser negativo.',
                    ],
                ];
            } elseif ($contentLengthValue > $maxContentLength) {
                $error = [
                    'status' => Response::HTTP_REQUEST_ENTITY_TOO_LARGE,
                    'payload' => [
                        'error' => 'Payload demasiado grande',
                        'message' => sprintf(
                            'El tamaño de la petición (%sMB) excede el límite permitido de %sMB.',
                            round($contentLengthValue / 1024 / 1024, 2),
                            round($maxContentLength / 1024 / 1024, 2)
                        ),
                        'max_size_bytes' => $maxContentLength,
                        'request_size_bytes' => $contentLengthValue,
                    ],
                ];
            }
        }

        if ($error !== null) {
            return response()->json($error['payload'], $error['status']);
        }

        return $next($request);
    }
}
