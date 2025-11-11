<?php

namespace App\Http\Controllers\Complementarios;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ComplementarioOfertado;
use App\Models\AspiranteComplementario;
use App\Models\Persona;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use setasign\Fpdi\Fpdi;
use App\Services\AspiranteDocumentoService;
use App\Services\AspiranteComplementarioService;

class AspiranteComplementarioController extends Controller
{
    protected $documentoService;
    protected $complementarioService;

    public function __construct(AspiranteDocumentoService $documentoService)
    {
        $this->documentoService = $documentoService;
        $this->complementarioService = new AspiranteComplementarioService($documentoService);
    }

    /**
     * Mostrar gestión de aspirantes (Admin)
     */
    public function gestionAspirantes()
    {
        $programas = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])->get();

        // Add aspirantes count for each program
        $programas->each(function ($programa) {
            $programa->aspirantes_count = AspiranteComplementario::where('complementario_id', $programa->id)->count();
        });

        return view('complementarios.gestion_aspirantes', compact('programas'));
    }

    /**
     * Mostrar aspirantes de un programa específico
     */
    public function verAspirantes($curso)
    {
        // Find program by name (assuming curso is the program name)
        $programa = ComplementarioOfertado::where('nombre', str_replace('-', ' ', $curso))->firstOrFail();

        // Get aspirantes for this program
        $aspirantes = AspiranteComplementario::with(['persona', 'complementario'])
            ->where('complementario_id', $programa->id)
            ->get();

        // Check for existing validation progress for this program
        $existingProgress = \App\Models\SofiaValidationProgress::where('complementario_id', $programa->id)
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        return view('complementarios.ver_aspirantes', compact('programa', 'aspirantes', 'existingProgress'));
    }

    /**
     * Agregar aspirante existente a un programa complementario
     */
    public function agregarAspirante(Request $request, $complementarioId)
    {
        $request->validate([
            'numero_documento' => 'required|string|max:191',
        ]);

        try {
            // Verificar que el programa existe
            ComplementarioOfertado::findOrFail($complementarioId);

            // Buscar persona por número de documento
            $persona = Persona::where('numero_documento', $request->numero_documento)->first();

            if (!$persona) {
                return $this->createErrorResponse(
                    'No se encontró ninguna persona registrada con el número de documento "' .
                        $request->numero_documento . '".'
                );
            }

            // Verificar si ya está inscrita en este programa
            $aspiranteExistente = AspiranteComplementario::where('persona_id', $persona->id)
                ->where('complementario_id', $complementarioId)
                ->first();

            if ($aspiranteExistente) {
                return $this->createErrorResponse(
                    'La persona con documento "' . $request->numero_documento .
                        '" ya se encuentra inscrita en este programa complementario.'
                );
            }

            // Crear nuevo aspirante - ahora permite múltiples programas por persona
            AspiranteComplementario::create([
                'persona_id' => $persona->id,
                'complementario_id' => $complementarioId,
                'estado' => 1, // Estado "En proceso"
                'observaciones' => 'Agregado manualmente desde gestión de aspirantes'
            ]);

            return $this->createSuccessResponse(
                'Aspirante agregado exitosamente. ' . $persona->primer_nombre . ' ' .
                    $persona->primer_apellido . ' ha sido inscrito en el programa.'
            );
        } catch (\Exception $e) {
            return $this->handleAspiranteException($e, $complementarioId, $request->numero_documento);
        }
    }

    /**
     * Crear respuesta JSON de error
     */
    private function createErrorResponse($message)
    {
        return response()->json([
            'success' => false,
            'message' => $message
        ]);
    }

    /**
     * Crear respuesta JSON de éxito
     */
    private function createSuccessResponse($message)
    {
        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Manejar excepciones en operaciones de aspirantes
     */
    private function handleAspiranteException(\Exception $e, $complementarioId, $numeroDocumento)
    {
        Log::error('Error agregando aspirante: ' . $e->getMessage(), [
            'complementario_id' => $complementarioId,
            'numero_documento' => $numeroDocumento,
            'exception' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Error interno del servidor. Por favor intente nuevamente.'
        ], 500);
    }

    /**
     * Rechazar aspirante de un programa complementario (cambiar estado a rechazado)
     */
    public function eliminarAspirante($complementarioId, $aspiranteId)
    {
        try {
            // Verificar que el programa existe
            ComplementarioOfertado::findOrFail($complementarioId);

            // Verificar que el aspirante existe y pertenece al programa
            $aspirante = AspiranteComplementario::where('id', $aspiranteId)
                ->where('complementario_id', $complementarioId)
                ->with('persona')
                ->firstOrFail();

            // Verificar permisos del usuario (solo administradores pueden rechazar)
            if (!auth()->user()->can('ELIMINAR ASPIRANTE COMPLEMENTARIO')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tiene permisos para rechazar aspirantes.'
                ], 403);
            }

            // Guardar información del aspirante para el mensaje
            $personaNombre = $aspirante->persona->primer_nombre . ' ' . $aspirante->persona->primer_apellido;
            $numeroDocumento = $aspirante->persona->numero_documento;

            // Cambiar el estado a rechazado (2) en lugar de eliminar
            $aspirante->estado = 2;
            $aspirante->save();

            Log::info('Aspirante rechazado exitosamente', [
                'aspirante_id' => $aspiranteId,
                'complementario_id' => $complementarioId,
                'persona_id' => $aspirante->persona_id,
                'user_id' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Aspirante rechazado exitosamente. ' .
                    $personaNombre . ' (' . $numeroDocumento . ') ha sido marcado como rechazado en el programa.'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Aspirante o programa no encontrado.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error rechazando aspirante: ' . $e->getMessage(), [
                'complementario_id' => $complementarioId,
                'aspirante_id' => $aspiranteId,
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor. Por favor intente nuevamente.'
            ], 500);
        }
    }

    /**
     * Exportar aspirantes a Excel
     */
    public function exportarAspirantesExcel($complementarioId)
    {
        try {
            // Verificar que el programa existe
            $programa = ComplementarioOfertado::findOrFail($complementarioId);

            // Obtener todos los aspirantes del programa con sus datos de persona y caracterización
            $aspirantes = AspiranteComplementario::with(['persona.caracterizacion', 'persona.tipoDocumento'])
                ->where('complementario_id', $complementarioId)
                ->get();

            // Crear nueva hoja de cálculo
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Establecer encabezados
            $sheet->setCellValue('A1', 'Tipo Documento');
            $sheet->setCellValue('B1', 'Número Documento');
            $sheet->setCellValue('C1', 'Caracterización');

            // Estilo para encabezados
            $headerStyle = [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '007BFF'],
                ],
            ];
            $sheet->getStyle('A1:C1')->applyFromArray($headerStyle);

            // Llenar datos
            $row = 2;
            foreach ($aspirantes as $aspirante) {
                $tipoDocumento = $aspirante->persona->tipoDocumento ? $aspirante->persona->tipoDocumento->name : 'N/A';
                $numeroDocumento = $aspirante->persona->numero_documento;
                $caracterizacion = $aspirante->persona->caracterizacion ?
                    $aspirante->persona->caracterizacion->nombre : 'Sin caracterización';

                $sheet->setCellValue('A' . $row, $tipoDocumento);
                $sheet->setCellValue('B' . $row, $numeroDocumento);
                $sheet->setCellValue('C' . $row, $caracterizacion);

                $row++;
            }

            // Auto-ajustar columnas
            foreach (range('A', 'C') as $column) {
                $sheet->getColumnDimension($column)->setAutoSize(true);
            }

            // Crear nombre del archivo
            $fileName = 'aspirantes_' . str_replace(' ', '_', $programa->nombre) . '_' .
                now()->format('Y-m-d_H-i-s') . '.xlsx';

            // Crear respuesta de descarga
            $response = new StreamedResponse(function () use ($spreadsheet) {
                $writer = new Xlsx($spreadsheet);
                $writer->save('php://output');
            });

            $response->headers->set(
                'Content-Type',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            );
            $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
            $response->headers->set('Cache-Control', 'max-age=0');

            return $response;
        } catch (\Exception $e) {
            Log::error('Error exportando aspirantes a Excel: ' . $e->getMessage(), [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al generar el archivo Excel. Por favor intente nuevamente.'
            ], 500);
        }
    }

    /**
     * Descargar cédulas de aspirantes en un archivo PDF combinado
     */
    public function descargarCedulas($complementarioId)
    {
        try {
            // Verificar que el programa existe
            $programa = ComplementarioOfertado::findOrFail($complementarioId);

            // Obtener aspirantes con documentos
            $aspirantes = $this->complementarioService->getAspirantesConDocumentos($complementarioId);

            if ($aspirantes->isEmpty()) {
                return back()->with('error', 'No hay aspirantes con documentos de identidad para descargar.');
            }

            $tempDir = $this->documentoService->createTempDirectory();
            $pdf = new Fpdi();

            $resultados = $this->complementarioService->procesarDescargaDocumentos($aspirantes, $pdf, $tempDir);

            if ($resultados['archivos_agregados'] === 0) {
                $this->documentoService->limpiarArchivosTemporales($resultados['archivos_temporales']);
                return back()->with(
                    'error',
                    'No se pudieron descargar los documentos. Verifique que los archivos existan en Google Drive.'
                );
            }

            return $this->complementarioService->generarArchivoPDF(
                $programa,
                $pdf,
                $tempDir,
                $resultados['archivos_temporales']
            );
        } catch (\Exception $e) {
            Log::error('Error descargando cédulas: ' . $e->getMessage(), [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al generar el archivo PDF. Por favor intente nuevamente.');
        }
    }


    /**
     * Validar documentos de aspirantes en Google Drive
     */
    public function validarDocumentos($complementarioId)
    {
        try {
            // Verificar que el programa existe
            ComplementarioOfertado::findOrFail($complementarioId);

            // Obtener todos los aspirantes del programa
            $aspirantes = AspiranteComplementario::with(['persona.tipoDocumento'])
                ->where('complementario_id', $complementarioId)
                ->get();

            if ($aspirantes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No hay aspirantes en este programa para validar documentos.'
                ]);
            }

            $files = $this->documentoService->getGoogleDriveFiles();
            $resultados = $this->complementarioService->procesarValidacionDocumentos($aspirantes, $files);

            Log::info("Validación de documentos completada", [
                'complementario_id' => $complementarioId,
                'total' => $resultados['total'],
                'con_documento' => $resultados['con_documento'],
                'sin_documento' => $resultados['sin_documento'],
                'errores' => $resultados['errores']
            ]);

            return response()->json([
                'success' => true,
                'message' => "Validación completada. Total: {$resultados['total']}, " .
                    "Con documento: {$resultados['con_documento']}, " .
                    "Sin documento: {$resultados['sin_documento']}" .
                    ($resultados['errores'] > 0 ? ", Errores: {$resultados['errores']}" : ""),
                'total' => $resultados['total'],
                'con_documento' => $resultados['con_documento'],
                'sin_documento' => $resultados['sin_documento'],
                'errores' => $resultados['errores']
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Programa no encontrado.'
            ], 404);
        } catch (\Exception $e) {
            Log::error('Error validando documentos: ' . $e->getMessage(), [
                'complementario_id' => $complementarioId,
                'user_id' => auth()->id(),
                'exception' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ], 500);
        }
    }

}
