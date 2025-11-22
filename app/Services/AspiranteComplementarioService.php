<?php

namespace App\Services;

use App\Models\AspiranteComplementario;
use App\Models\ComplementarioOfertado;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use setasign\Fpdi\Fpdi;

class AspiranteComplementarioService
{
    protected $documentoService;

    public function __construct(AspiranteDocumentoService $documentoService)
    {
        $this->documentoService = $documentoService;
    }

    /**
     * Obtener aspirantes con documentos
     */
    public function getAspirantesConDocumentos($complementarioId)
    {
        return AspiranteComplementario::with(['persona.tipoDocumento'])
            ->where('complementario_id', $complementarioId)
            ->whereHas('persona', function ($query) {
                $query->where('condocumento', 1);
            })
            ->get()
            ->sortBy(function ($aspirante) {
                return $aspirante->persona->numero_documento;
            });
    }

    /**
     * Procesar descarga de documentos
     */
    public function procesarDescargaDocumentos($aspirantes, Fpdi $pdf, $tempDir)
    {
        $archivosAgregados = 0;
        $archivosTemporales = [];

        foreach ($aspirantes as $aspirante) {
            try {
                $resultado = $this->procesarDocumentoIndividual($aspirante, $pdf, $tempDir, $archivosAgregados);
                if ($resultado['exito']) {
                    $archivosAgregados++;
                    $archivosTemporales[] = $resultado['archivo_temporal'];
                }
            } catch (\Exception $e) {
                $this->logErrorProcesamientoDocumento($aspirante, $e);
                continue;
            }
        }

        return [
            'archivos_agregados' => $archivosAgregados,
            'archivos_temporales' => $archivosTemporales
        ];
    }

    /**
     * Procesar documento individual
     */
    private function procesarDocumentoIndividual($aspirante, Fpdi $pdf, $tempDir, $indice)
    {
        $persona = $aspirante->persona;
        $patron = $this->documentoService->construirPatronBusqueda($persona);

        $matchingFile = $this->documentoService->encontrarArchivoEnGoogleDrive($patron);

        if (!$matchingFile || !\Illuminate\Support\Facades\Storage::disk('google')->exists($matchingFile)) {
            $this->logArchivoNoEncontrado($aspirante, $persona);
            return ['exito' => false];
        }

        $fileContent = \Illuminate\Support\Facades\Storage::disk('google')->get($matchingFile);
        $tempFilePath = $tempDir . '/temp_' . $indice . '_' . $persona->numero_documento . '.pdf';

        file_put_contents($tempFilePath, $fileContent);
        $this->documentoService->agregarPaginasAPDF($pdf, $tempFilePath);

        return [
            'exito' => true,
            'archivo_temporal' => $tempFilePath
        ];
    }

    /**
     * Generar archivo PDF final
     */
    public function generarArchivoPDF($programa, Fpdi $pdf, $tempDir, $archivosTemporales)
    {
        $pdfFileName = 'cedulas_' . str_replace(' ', '_', $programa->nombre) . '_' .
            now()->format('Y-m-d_H-i-s') . '.pdf';
        $pdfPath = $tempDir . '/' . $pdfFileName;

        $pdf->Output('F', $pdfPath);

        $this->documentoService->limpiarArchivosTemporales($archivosTemporales);

        return response()->download($pdfPath, $pdfFileName)->deleteFileAfterSend(true);
    }

    /**
     * Log de archivo no encontrado
     */
    private function logArchivoNoEncontrado($aspirante, $persona)
    {
        Log::warning('Archivo no encontrado en Google Drive', [
            'aspirante_id' => $aspirante->id,
            'persona_id' => $persona->id,
            'numero_documento' => $persona->numero_documento
        ]);
    }

    /**
     * Log de error procesando documento
     */
    private function logErrorProcesamientoDocumento($aspirante, \Exception $e)
    {
        Log::error('Error procesando archivo PDF: ' . $e->getMessage(), [
            'aspirante_id' => $aspirante->id,
            'persona_id' => $aspirante->persona_id ?? 'N/A',
            'exception' => $e->getTraceAsString()
        ]);
    }

    /**
     * Procesar validación de documentos
     */
    public function procesarValidacionDocumentos($aspirantes, $files)
    {
        $totalAspirantes = $aspirantes->count();
        $conDocumento = 0;
        $sinDocumento = 0;
        $errores = 0;

        foreach ($aspirantes as $aspirante) {
            try {
                $persona = $aspirante->persona;
                $patron = $this->documentoService->construirPatronBusqueda($persona);

                $tieneDocumento = $this->documentoService->buscarDocumentoEnGoogleDrive($files, $patron);

                // Actualizar condocumento en la persona
                $persona->update(['condocumento' => $tieneDocumento ? 1 : 0]);

                if ($tieneDocumento) {
                    $conDocumento++;
                } else {
                    $sinDocumento++;
                }
            } catch (\Exception $e) {
                $errores++;
                Log::error("Error validando documento para aspirante {$aspirante->id}", [
                    'aspirante_id' => $aspirante->id,
                    'persona_id' => $aspirante->persona_id,
                    'exception' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        return [
            'total' => $totalAspirantes,
            'con_documento' => $conDocumento,
            'sin_documento' => $sinDocumento,
            'errores' => $errores
        ];
    }
}
