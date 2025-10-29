<?php

namespace App\Services;

use App\Models\Aprendiz;
use App\Models\Instructor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Endroid\QrCode\QrCode;

class CarnetService
{
    /**
     * Genera carnet para un aprendiz
     *
     * @param Aprendiz $aprendiz
     * @return string Path del archivo generado
     */
    public function generarCarnetAprendiz(Aprendiz $aprendiz): string
    {
        try {
            $persona = $aprendiz->persona;
            $ficha = $aprendiz->fichaCaracterizacion;

            // Generar código QR con datos del aprendiz
            $qrData = json_encode([
                'tipo' => 'APRENDIZ',
                'id' => $aprendiz->id,
                'documento' => $persona->numero_documento,
                'ficha' => $ficha->ficha ?? 'N/A',
                'generado' => now()->toDateString(),
            ]);

            $qrCode = QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->generate($qrData);

            // Crear imagen del carnet
            $carnet = $this->crearPlantillaCarnet('aprendiz');

            // Agregar datos del aprendiz
            $this->agregarDatosCarnet($carnet, [
                'nombre' => $persona->nombre_completo,
                'documento' => $persona->numero_documento,
                'ficha' => $ficha->ficha ?? 'N/A',
                'programa' => $ficha->programaFormacion->nombre ?? 'N/A',
                'tipo' => 'APRENDIZ',
            ]);

            // Agregar QR
            $this->agregarQRCarnet($carnet, $qrCode);

            // Guardar carnet
            $filename = "carnets/aprendices/carnet_{$aprendiz->id}_{$persona->numero_documento}.png";
            $carnet->save(storage_path("app/public/{$filename}"));

            Log::info('Carnet de aprendiz generado', [
                'aprendiz_id' => $aprendiz->id,
                'archivo' => $filename,
            ]);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error generando carnet de aprendiz', [
                'aprendiz_id' => $aprendiz->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Genera carnet para un instructor
     *
     * @param Instructor $instructor
     * @return string Path del archivo generado
     */
    public function generarCarnetInstructor(Instructor $instructor): string
    {
        try {
            $persona = $instructor->persona;

            // Generar código QR
            $qrData = json_encode([
                'tipo' => 'INSTRUCTOR',
                'id' => $instructor->id,
                'documento' => $persona->numero_documento,
                'regional' => $instructor->regional->nombre ?? 'N/A',
                'generado' => now()->toDateString(),
            ]);

            $qrCode = QrCode::format('png')
                ->size(200)
                ->margin(1)
                ->generate($qrData);

            // Crear imagen del carnet
            $carnet = $this->crearPlantillaCarnet('instructor');

            // Agregar datos del instructor
            $this->agregarDatosCarnet($carnet, [
                'nombre' => $persona->nombre_completo,
                'documento' => $persona->numero_documento,
                'regional' => $instructor->regional->nombre ?? 'N/A',
                'especialidad' => $instructor->especialidades['principal'] ?? 'N/A',
                'tipo' => 'INSTRUCTOR',
            ]);

            // Agregar QR
            $this->agregarQRCarnet($carnet, $qrCode);

            // Guardar carnet
            $filename = "carnets/instructores/carnet_{$instructor->id}_{$persona->numero_documento}.png";
            $carnet->save(storage_path("app/public/{$filename}"));

            Log::info('Carnet de instructor generado', [
                'instructor_id' => $instructor->id,
                'archivo' => $filename,
            ]);

            return $filename;
        } catch (\Exception $e) {
            Log::error('Error generando carnet de instructor', [
                'instructor_id' => $instructor->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verifica un carnet mediante código QR
     *
     * @param string $qrData
     * @return array
     */
    public function verificarCarnet(string $qrData): array
    {
        try {
            $datos = json_decode($qrData, true);

            if (!$datos || !isset($datos['tipo']) || !isset($datos['id'])) {
                return [
                    'valido' => false,
                    'mensaje' => 'Código QR inválido',
                ];
            }

            if ($datos['tipo'] === 'APRENDIZ') {
                $aprendiz = Aprendiz::with('persona', 'fichaCaracterizacion')->find($datos['id']);
                
                if (!$aprendiz) {
                    return [
                        'valido' => false,
                        'mensaje' => 'Aprendiz no encontrado',
                    ];
                }

                return [
                    'valido' => true,
                    'tipo' => 'APRENDIZ',
                    'datos' => [
                        'nombre' => $aprendiz->persona->nombre_completo,
                        'documento' => $aprendiz->persona->numero_documento,
                        'ficha' => $aprendiz->fichaCaracterizacion->ficha ?? 'N/A',
                        'estado' => $aprendiz->estado ? 'Activo' : 'Inactivo',
                    ],
                ];
            } elseif ($datos['tipo'] === 'INSTRUCTOR') {
                $instructor = Instructor::with('persona', 'regional')->find($datos['id']);
                
                if (!$instructor) {
                    return [
                        'valido' => false,
                        'mensaje' => 'Instructor no encontrado',
                    ];
                }

                return [
                    'valido' => true,
                    'tipo' => 'INSTRUCTOR',
                    'datos' => [
                        'nombre' => $instructor->persona->nombre_completo,
                        'documento' => $instructor->persona->numero_documento,
                        'regional' => $instructor->regional->nombre ?? 'N/A',
                        'estado' => $instructor->status ? 'Activo' : 'Inactivo',
                    ],
                ];
            }

            return [
                'valido' => false,
                'mensaje' => 'Tipo de carnet desconocido',
            ];
        } catch (\Exception $e) {
            Log::error('Error verificando carnet', [
                'error' => $e->getMessage(),
            ]);

            return [
                'valido' => false,
                'mensaje' => 'Error al verificar carnet',
            ];
        }
    }

    /**
     * Crea plantilla básica del carnet
     *
     * @param string $tipo
     * @return mixed
     */
    protected function crearPlantillaCarnet(string $tipo)
    {
        // Crear imagen base (en producción usar plantilla real)
        $width = 600;
        $height = 400;
        
        $image = imagecreatetruecolor($width, $height);
        
        // Colores
        $background = $tipo === 'aprendiz' ? imagecolorallocate($image, 52, 152, 219) : imagecolorallocate($image, 41, 128, 185);
        $white = imagecolorallocate($image, 255, 255, 255);
        
        imagefilledrectangle($image, 0, 0, $width, $height, $background);
        
        return $image;
    }

    /**
     * Agrega datos al carnet
     *
     * @param mixed $image
     * @param array $datos
     * @return void
     */
    protected function agregarDatosCarnet($image, array $datos): void
    {
        // En producción usar librería de imágenes apropiada
        // Este es un placeholder
    }

    /**
     * Agrega QR al carnet
     *
     * @param mixed $image
     * @param string $qrCode
     * @return void
     */
    protected function agregarQRCarnet($image, string $qrCode): void
    {
        // En producción usar librería de imágenes apropiada
        // Este es un placeholder
    }
}

