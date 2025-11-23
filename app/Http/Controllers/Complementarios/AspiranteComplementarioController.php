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
use App\Services\ComplementarioService;
use App\Repositories\TemaRepository;
use App\Models\Pais;
use App\Models\Departamento;

class AspiranteComplementarioController extends Controller
{
    protected $documentoService;
    protected $complementarioService;
    protected $temaRepository;
    protected $complementarioServiceHelper;

    public function __construct(
        AspiranteDocumentoService $documentoService,
        TemaRepository $temaRepository,
        ComplementarioService $complementarioServiceHelper
    ) {
        $this->documentoService = $documentoService;
        $this->complementarioService = new AspiranteComplementarioService($documentoService);
        $this->temaRepository = $temaRepository;
        $this->complementarioServiceHelper = $complementarioServiceHelper;
    }

    /**
     * Mostrar lista de programas complementarios (Index de aspirantes)
     */
    public function index()
    {
        $programas = ComplementarioOfertado::with(['modalidad.parametro', 'jornada', 'diasFormacion'])->get();

        // Add aspirantes count for each program
        $programas->each(function ($programa) {
            $programa->aspirantes_count = AspiranteComplementario::where('complementario_id', $programa->id)->count();
        });

        return view('complementarios.aspirantes.index', compact('programas'));
    }

    /**
     * Mostrar aspirantes de un programa específico
     */
    public function programa(ComplementarioOfertado $programa)
    {
        // Get aspirantes for this program
        $aspirantes = AspiranteComplementario::with(['persona', 'complementario'])
            ->where('complementario_id', $programa->id)
            ->get();

        // Check for existing validation progress for this program
        $existingProgress = \App\Models\SofiaValidationProgress::where('complementario_id', $programa->id)
            ->whereIn('status', ['pending', 'processing'])
            ->first();

        return view('complementarios.aspirantes.programa', compact('programa', 'aspirantes', 'existingProgress'));
    }

    /**
     * Buscar persona por número de documento
     */
    public function buscarPersona(Request $request)
    {
        $request->validate([
            'numero_documento' => 'required|string|max:191',
        ]);

        $persona = Persona::where('numero_documento', $request->numero_documento)->first();

        if (!$persona) {
            return response()->json([
                'success' => false,
                'found' => false,
                'message' => 'No se encontró ninguna persona con este número de documento.',
            ]);
        }

        // Verificar si ya está inscrita en algún programa (opcional, para mostrar info)
        $aspirantes = AspiranteComplementario::where('persona_id', $persona->id)->count();

        return response()->json([
            'success' => true,
            'found' => true,
            'persona' => [
                'id' => $persona->id,
                'numero_documento' => $persona->numero_documento,
                'nombre_completo' => trim(($persona->primer_nombre ?? '') . ' ' . 
                                         ($persona->segundo_nombre ?? '') . ' ' . 
                                         ($persona->primer_apellido ?? '') . ' ' . 
                                         ($persona->segundo_apellido ?? '')),
                'email' => $persona->email ?? 'No registrado',
                'telefono' => $persona->telefono ?? $persona->celular ?? 'No registrado',
                'aspirantes_count' => $aspirantes,
            ],
        ]);
    }

    /**
     * Mostrar formulario para crear nuevo aspirante
     */
    public function create(Request $request, ComplementarioOfertado $programa)
    {
        // Obtener datos para el formulario
        $documentos = $this->buildTemaPayload(
            $this->temaRepository->obtenerTiposDocumento(),
            $this->complementarioServiceHelper->getTiposDocumento()
        );
        $generos = $this->buildTemaPayload(
            $this->temaRepository->obtenerGeneros(),
            $this->complementarioServiceHelper->getGeneros()
        );
        $caracterizaciones = $this->buildTemaPayload(
            $this->temaRepository->obtenerCaracterizacionesComplementarias()
        );
        $vias = $this->buildTemaPayload($this->temaRepository->obtenerVias());
        $letras = $this->buildTemaPayload($this->temaRepository->obtenerLetras());
        $cardinales = $this->buildTemaPayload($this->temaRepository->obtenerCardinales());

        $paises = Pais::where('status', 1)->get();
        $departamentos = Departamento::where('status', 1)->get();
        $municipios = collect();

        // Prellenar datos si se viene desde búsqueda sin encontrar persona
        if ($request->has('numero_documento')) {
            // Agregar a old() para que el formulario lo prellene
            $request->flash();
        }

        return view('complementarios.aspirantes.create', compact(
            'programa',
            'documentos',
            'generos',
            'caracterizaciones',
            'vias',
            'letras',
            'cardinales',
            'paises',
            'departamentos',
            'municipios'
        ));
    }

    /**
     * Almacenar nuevo aspirante
     */
    public function store(Request $request, ComplementarioOfertado $programa)
    {
        $validated = $request->validate([
            'tipo_documento' => 'required|integer',
            'numero_documento' => 'required|string|max:191',
            'primer_nombre' => 'required|string|max:191',
            'segundo_nombre' => 'nullable|string|max:191',
            'primer_apellido' => 'required|string|max:191',
            'segundo_apellido' => 'nullable|string|max:191',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|integer',
            'telefono' => 'nullable|string|max:191',
            'celular' => 'nullable|string|max:191',
            'email' => 'nullable|email|max:191',
            'pais_id' => 'required|integer',
            'departamento_id' => 'required|integer',
            'municipio_id' => 'required|integer',
            'direccion' => 'nullable|string|max:191',
            'observaciones' => 'nullable|string',
        ]);

        try {
            // Verificar si la persona ya existe
            $personaExistente = Persona::where('numero_documento', $validated['numero_documento'])
                ->orWhere(function ($query) use ($validated) {
                    if (!empty($validated['email'])) {
                        $query->where('email', $validated['email']);
                    }
                })
                ->first();

            if ($personaExistente) {
                // Si existe, verificar si ya está inscrita en este programa
                $aspiranteExistente = AspiranteComplementario::where('persona_id', $personaExistente->id)
                    ->where('complementario_id', $programa->id)
                    ->first();

                if ($aspiranteExistente) {
                    return redirect()->back()
                        ->withInput()
                        ->with('error', 'La persona ya está inscrita en este programa complementario.');
                }

                // Si existe pero no está inscrita, actualizar datos y crear aspirante
                $personaExistente->update($validated);
                $persona = $personaExistente;
            } else {
                // Crear nueva persona
                $persona = Persona::create($validated + [
                    'user_create_id' => auth()->id() ?? 1,
                    'user_edit_id' => auth()->id() ?? 1,
                ]);
            }

            // Crear aspirante
            AspiranteComplementario::create([
                'persona_id' => $persona->id,
                'complementario_id' => $programa->id,
                'estado' => 1, // En proceso
                'observaciones' => $validated['observaciones'] ?? 'Creado desde formulario de aspirantes',
            ]);

            return redirect()
                ->route('aspirantes.programa', ['programa' => $programa->id])
                ->with('success', 'Aspirante creado exitosamente.');
        } catch (\Exception $e) {
            Log::error('Error al crear aspirante', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'programa_id' => $programa->id,
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el aspirante. Por favor, inténtalo nuevamente.');
        }
    }

    /**
     * Construir payload de tema para el formulario
     */
    private function buildTemaPayload($tema = null, $parametros = null)
    {
        if ($tema && $tema->parametros && $tema->parametros->count() > 0) {
            return $tema;
        }

        if ($parametros && $parametros->count() > 0) {
            return (object) [
                'id' => null,
                'parametros' => $parametros,
            ];
        }

        return (object) [
            'id' => null,
            'parametros' => collect(),
        ];
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
