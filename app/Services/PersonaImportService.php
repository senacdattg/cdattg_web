<?php

namespace App\Services;

use App\Exceptions\ImportFileNotFoundException;
use App\Exceptions\ImportHeaderMismatchException;
use App\Exceptions\MissingDocumentTypeException;
use App\Jobs\ProcessPersonaImportJob;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\PersonaContactAlert;
use App\Models\PersonaImport;
use App\Models\PersonaImportIssue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;
use App\Services\PersonaImportNormalizer;
use App\Services\PersonaService;
use App\Repositories\TemaRepository;

class PersonaImportService
{
    private PersonaService $personaService;
    private TemaRepository $temaRepository;

    public function __construct(PersonaService $personaService, TemaRepository $temaRepository)
    {
        $this->personaService = $personaService;
        $this->temaRepository = $temaRepository;
    }
    
    private const CHUNK_SIZE = 250;
    private const DUPLICATE_ENTRY_TEXT = 'Duplicate entry';

    private const DOC_CEDULA_CIUDADANIA = 'CEDULA DE CIUDADANIA';
    private const DOC_CEDULA_EXTRANJERIA = 'CEDULA DE EXTRANJERIA';
    private const DOC_PASAPORTE = 'PASAPORTE';
    private const DOC_PERMISO_PROTECCION = 'PERMISO POR PROTECCION TEMPORAL';
    private const DOC_TARJETA_IDENTIDAD = 'TARJETA DE IDENTIDAD';
    private const DOC_REGISTRO_CIVIL = 'REGISTRO CIVIL';
    private const DOC_SIN_IDENTIFICACION = 'SIN IDENTIFICACION';

    private array $headerMap = [];
    private array $documentSeen = [];
    private bool $documentoCacheInitialized = false;
    private array $documentAliasMap = [
        // Cédula de Ciudadanía
        'CC' => self::DOC_CEDULA_CIUDADANIA,
        'C.C' => self::DOC_CEDULA_CIUDADANIA,
        'C.C.' => self::DOC_CEDULA_CIUDADANIA,
        'CEDULA DE CIUDADANIA' => self::DOC_CEDULA_CIUDADANIA,
        'CEDULA CIUDADANIA' => self::DOC_CEDULA_CIUDADANIA,
        'CEDULA DE CIUDADANÍA' => self::DOC_CEDULA_CIUDADANIA,
        'CEDULA CIUDADANÍA' => self::DOC_CEDULA_CIUDADANIA,
        'CEDULA' => self::DOC_CEDULA_CIUDADANIA,
        'CÉDULA' => self::DOC_CEDULA_CIUDADANIA,
        'CÉDULA DE CIUDADANÍA' => self::DOC_CEDULA_CIUDADANIA,
        // Cédula de Extranjería
        'CE' => self::DOC_CEDULA_EXTRANJERIA,
        'C.E' => self::DOC_CEDULA_EXTRANJERIA,
        'C.E.' => self::DOC_CEDULA_EXTRANJERIA,
        'CEDULA DE EXTRANJERIA' => self::DOC_CEDULA_EXTRANJERIA,
        'CEDULA EXTRANJERIA' => self::DOC_CEDULA_EXTRANJERIA,
        'CEDULA DE EXTRANJERÍA' => self::DOC_CEDULA_EXTRANJERIA,
        'CÉDULA DE EXTRANJERÍA' => self::DOC_CEDULA_EXTRANJERIA,
        // Pasaporte
        'PASAPORTE' => self::DOC_PASAPORTE,
        'PA' => self::DOC_PASAPORTE,
        'P.P' => self::DOC_PASAPORTE,
        'P.P.' => self::DOC_PASAPORTE,
        // Permiso por Protección Temporal
        'PPT' => self::DOC_PERMISO_PROTECCION,
        'PERMISO POR PROTECCION TEMPORAL' => self::DOC_PERMISO_PROTECCION,
        'PERMISO POR PROTECCIÓN TEMPORAL' => self::DOC_PERMISO_PROTECCION,
        'PERMISO PROTECCION TEMPORAL' => self::DOC_PERMISO_PROTECCION,
        'PERMISO PROTECCIÓN TEMPORAL' => self::DOC_PERMISO_PROTECCION,
        // Tarjeta de Identidad
        'TI' => self::DOC_TARJETA_IDENTIDAD,
        'T.I' => self::DOC_TARJETA_IDENTIDAD,
        'T.I.' => self::DOC_TARJETA_IDENTIDAD,
        'TARJETA DE IDENTIDAD' => self::DOC_TARJETA_IDENTIDAD,
        'TARJETA IDENTIDAD' => self::DOC_TARJETA_IDENTIDAD,
        // Registro Civil
        'RC' => self::DOC_REGISTRO_CIVIL,
        'R.C' => self::DOC_REGISTRO_CIVIL,
        'R.C.' => self::DOC_REGISTRO_CIVIL,
        'REGISTRO CIVIL' => self::DOC_REGISTRO_CIVIL,
        // Sin Identificación
        'SIN DOCUMENTO' => self::DOC_SIN_IDENTIFICACION,
        'SIN' => self::DOC_SIN_IDENTIFICACION,
        'SD' => self::DOC_SIN_IDENTIFICACION,
        'S/D' => self::DOC_SIN_IDENTIFICACION,
        'SIN IDENTIFICACION' => self::DOC_SIN_IDENTIFICACION,
        'SIN IDENTIFICACIÓN' => self::DOC_SIN_IDENTIFICACION,
        'NIS' => self::DOC_SIN_IDENTIFICACION,
    ];

    private array $documentoCache = [];

    public function iniciarImportacion(UploadedFile $file, int $userId): PersonaImport
    {
        $timestamp = now()->format('Ymd_His');
        $extension = strtolower($file->getClientOriginalExtension() ?: 'xlsx');
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $baseName = $baseName ? $baseName : 'archivo';
        $fileName = "{$timestamp}_usuario{$userId}_{$baseName}.{$extension}";

        $storedPath = $file->storeAs('carga_masiva', $fileName, 'local');

        $import = PersonaImport::create([
            'user_id' => $userId,
            'original_name' => $file->getClientOriginalName(),
            'disk' => 'local',
            'path' => $storedPath,
        ]);

        ProcessPersonaImportJob::dispatch($import->id);

        return $import;
    }

    public function procesar(PersonaImport $import): void
    {
        // Inicializar cache de tipos de documento (lazy loading)
        $this->warmDocumentoCache();
        
        $import->update([
            'status' => 'processing',
            'processed_rows' => 0,
            'success_count' => 0,
            'duplicate_count' => 0,
            'missing_contact_count' => 0,
        ]);

        $rutaArchivo = Storage::disk($import->disk)->path($import->path);

        if (!file_exists($rutaArchivo)) {
            throw new ImportFileNotFoundException($rutaArchivo);
        }

        $reader = IOFactory::createReaderForFile($rutaArchivo);
        $reader->setReadDataOnly(true);

        $spreadsheet = $reader->load($rutaArchivo);
        $hoja = $spreadsheet->getActiveSheet();
        $highestRow = $hoja->getHighestDataRow();
        unset($hoja);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        if ($highestRow <= 1) {
            $import->update([
                'total_rows' => 0,
                'status' => 'completed',
            ]);
            
            // Liberar recursos si no hay datos
            unset($reader);
            gc_collect_cycles();
        } else {
            $filter = new class implements IReadFilter {
                private int $startRow = 1;
                private int $endRow = 1;

                public function setRows(int $startRow, int $chunkSize): void
                {
                    $this->startRow = $startRow;
                    $this->endRow = $startRow + $chunkSize - 1;
                }

                public function readCell($columnAddress, $row, $worksheetName = ''): bool
                {
                    return $row >= $this->startRow && $row <= $this->endRow;
                }
            };

            $reader->setReadFilter($filter);

            // Establecer estimación inicial de total_rows (highestRow - 1 para excluir header)
            // Se actualizará con el valor real al finalizar
            $import->update([
                'total_rows' => $highestRow - 1,
            ]);

            // Procesar chunks y contar filas válidas al mismo tiempo
            $totalRowsValidas = $this->procesarChunks($reader, $rutaArchivo, $highestRow, $filter, $import);
            
            // Actualizar con el valor real de filas válidas procesadas
            $import->update([
                'total_rows' => $totalRowsValidas,
            ]);

            $import->update([
                'status' => 'completed',
            ]);
        }
        
        // Liberar explícitamente el reader y forzar recolección de basura
        unset($reader, $filter);
        gc_collect_cycles();
        
        // Esperar un momento adicional en Windows para asegurar liberación completa del archivo
        if (PHP_OS_FAMILY === 'Windows') {
            usleep(250000); // 0.25 segundos
            gc_collect_cycles();
        }
    }


    private function warmDocumentoCache(): void
    {
        // Lazy loading - solo cargar una vez
        if ($this->documentoCacheInitialized) {
            return;
        }
        
        if (! Schema::hasTable('parametros')) {
            Log::warning('La tabla "parametros" no existe; los tipos de documento se resolverán como null');
            $this->documentoCache = [];
            $this->documentoCacheInitialized = true;
            return;
        }

        // Obtener tipos de documento usando el repositorio existente
        $temaTiposDocumento = $this->temaRepository->obtenerTiposDocumento();
        
        if (!$temaTiposDocumento || !$temaTiposDocumento->parametros) {
            Log::warning('No se encontró el tema de tipos de documento (tema_id=2)');
            $this->documentoCache = [];
            $this->documentoCacheInitialized = true;
            return;
        }
        
        $this->documentoCache = [];
        $nombresNormalizados = [];
        foreach ($temaTiposDocumento->parametros as $parametro) {
            // Normalizar el nombre del parámetro removiendo tildes y convirtiéndolo a mayúsculas
            $nameOriginal = $parametro->name;
            $nameNormalized = $this->normalizarTextoSinTildes($nameOriginal);
            $this->documentoCache[$nameNormalized] = (int) $parametro->id;
            
            // Guardar para diagnóstico
            $nombresNormalizados[] = [
                'original' => $nameOriginal,
                'normalizado' => $nameNormalized,
                'id' => $parametro->id,
            ];
        }

        // Log para diagnóstico
        Log::info('Cache de tipos de documento inicializado', [
            'normalizaciones' => $nombresNormalizados,
            'cache_keys' => array_keys($this->documentoCache),
            'cache_values' => $this->documentoCache,
        ]);

        if (empty($this->documentoCache)) {
            Log::warning('No se encontraron parámetros en el tema de tipos de documento');
        }
        
        $this->documentoCacheInitialized = true;
    }

    /**
     * Normaliza texto removiendo tildes y convirtiéndolo a mayúsculas
     */
    private function normalizarTextoSinTildes(string $texto): string
    {
        // Convertir primero a mayúsculas para tener consistencia
        $texto = mb_strtoupper($texto, 'UTF-8');
        
        // Reemplazar TODOS los caracteres con tilde (mayúsculas porque ya convertimos)
        $texto = str_replace(
            ['Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'Ü'],
            ['A', 'E', 'I', 'O', 'U', 'N', 'U'],
            $texto
        );
        
        return $texto;
    }

    private function resolverEncabezados(array $row): array
    {
        $map = [];

        $headerTargets = [
            'tipo_documento' => [
                'tipo de documento',
                'tipodocumento',
                'tipo documento',
                'tipo_documento',
                'tipo documento identidad',
                'documento tipo',
                'td',
                'tipo doc',
            ],
            'numero_documento' => [
                'numero de documento',
                'número de documento',
                'numero documento',
                'documento',
                'documento identidad',
            ],
            'primer_nombre' => [
                'primer nombre',
                'nombre',
                'nombre1',
            ],
            'segundo_nombre' => [
                'segundo nombre',
                'nombre2',
            ],
            'primer_apellido' => [
                'primer apellido',
                'apellido',
                'apellido1',
            ],
            'segundo_apellido' => [
                'segundo apellido',
                'apellido2',
            ],
            'email' => [
                'correo electronico',
                'correo electrónico',
                'correo',
                'email',
            ],
            'celular' => [
                'celular',
                'telefono celular',
                'teléfono celular',
                'movil',
                'móvil',
            ],
            'telefono' => [
                'telefono',
                'teléfono',
                'telefono fijo',
                'teléfono fijo',
            ],
        ];

        foreach ($row as $column => $value) {
            $normalized = PersonaImportNormalizer::normalizarTexto($value ?? '');

            foreach ($headerTargets as $field => $targets) {
                if (in_array($normalized, $targets, true)) {
                    $map[$field] = $column;
                    Log::debug('Encabezado mapeado', [
                        'campo' => $field,
                        'columna' => $column,
                        'valor_original' => $value,
                        'valor_normalizado' => $normalized,
                    ]);
                    break;
                }
            }
        }

        $requeridos = ['tipo_documento', 'numero_documento', 'primer_nombre', 'primer_apellido'];

        foreach ($requeridos as $campo) {
            if (!array_key_exists($campo, $map)) {
                Log::warning('Encabezado requerido no encontrado', [
                    'campo_faltante' => $campo,
                    'encabezados_disponibles' => array_map(
                        fn($v) => PersonaImportNormalizer::normalizarTexto($v ?? ''),
                        $row
                    ),
                ]);
                return [];
            }
        }

        Log::info('Mapeo de encabezados completado', ['map' => $map]);

        return $map;
    }

    private function mapearFila(array $row): array
    {
        $datos = [];

        foreach ($this->headerMap as $field => $column) {
            $valor = $row[$column] ?? null;
            $datos[$field] = is_string($valor) ? trim($valor) : $valor;
        }

        // Log para diagnóstico: ver qué valor se está leyendo del tipo de documento
        $valorTipoDocumentoOriginal = $datos['tipo_documento'] ?? null;
        if ($valorTipoDocumentoOriginal) {
            Log::debug('Valor tipo documento leído del Excel', [
                'valor_original' => $valorTipoDocumentoOriginal,
                'tipo' => gettype($valorTipoDocumentoOriginal),
            ]);
        }

        $datos['numero_documento'] = PersonaImportNormalizer::limpiarNumeroDocumento($datos['numero_documento'] ?? '');
        $datos['primer_nombre'] = $datos['primer_nombre'] ? Str::upper($datos['primer_nombre']) : null;
        $datos['segundo_nombre'] = $datos['segundo_nombre'] ? Str::upper($datos['segundo_nombre']) : null;
        $datos['primer_apellido'] = $datos['primer_apellido'] ? Str::upper($datos['primer_apellido']) : null;
        $datos['segundo_apellido'] = $datos['segundo_apellido'] ? Str::upper($datos['segundo_apellido']) : null;
        $datos['email'] = PersonaImportNormalizer::normalizarEmail($datos['email'] ?? null);
        $datos['celular'] = PersonaImportNormalizer::normalizarTelefono($datos['celular'] ?? null);
        $datos['telefono'] = PersonaImportNormalizer::normalizarTelefono($datos['telefono'] ?? null);
        $datos['tipo_documento_id'] = $this->resolverTipoDocumentoId($datos['tipo_documento'] ?? null);

        // Log si no se resolvió el tipo de documento
        if (!$datos['tipo_documento_id'] && $valorTipoDocumentoOriginal) {
            $valorNormalizado = $valorTipoDocumentoOriginal
                ? Str::upper(Str::ascii(trim($valorTipoDocumentoOriginal)))
                : null;
            Log::warning('No se pudo resolver el tipo de documento', [
                'valor_original' => $valorTipoDocumentoOriginal,
                'valor_normalizado' => $valorNormalizado,
            ]);
        }

        return $datos;
    }

    private function filaVacia(array $row): bool
    {
        $valores = array_filter($row, fn($value) => !empty($value));
        return empty($valores);
    }

    private function consultarExistencias(array $records): array
    {
        $documentos = array_values(
            array_unique(
                array_filter(
                    array_map(fn($item) => $item['data']['numero_documento'] ?? null, $records)
                )
            )
        );

        if (empty($documentos)) {
            return ['documentos' => []];
        }

        $existentes = Persona::whereIn('numero_documento', $documentos)
            ->pluck('numero_documento')
            ->map(fn($doc) => (string) $doc)
            ->toArray();

        return [
            'documentos' => $existentes,
        ];
    }

    private function validarDuplicados(
        array $data,
        array $existsCaches,
        PersonaImport $import,
        int $rowNumber,
        array $raw
    ): array {
        $esDuplicado = false;
        $tipoDocumentoId = $data['tipo_documento_id'] ?? null;
        $numeroDocumento = $data['numero_documento'] ?? null;
        $email = $data['email'] ?? null;
        $celular = $data['celular'] ?? null;
        $issueType = null;

        // Validar que el tipo de documento sea válido (requerido)
        if (!$tipoDocumentoId) {
            $esDuplicado = true;
            $issueType = 'missing_document_type';
            $tipoDocumentoId = null;
        } elseif (! $numeroDocumento) {
            $esDuplicado = true;
            $issueType = 'missing_document';
        } elseif (empty($data['primer_nombre']) || empty($data['primer_apellido'])) {
            $esDuplicado = true;
            $issueType = 'missing_required_fields';
        } elseif (isset($this->documentSeen[$numeroDocumento])) {
            $esDuplicado = true;
            $issueType = 'duplicate_document_in_file';
        } elseif (in_array($numeroDocumento, $existsCaches['documentos'], true)) {
            $esDuplicado = true;
            $issueType = 'duplicate_document_existing';
        } else {
            $this->documentSeen[$numeroDocumento] = true;
        }

        // Registrar issue si es necesario
        if ($issueType) {
            $this->registrarIssue(
                $import,
                $rowNumber,
                $issueType,
                $numeroDocumento,
                $email,
                $celular,
                $raw
            );
        }

        return [
            'es_duplicado' => $esDuplicado,
            'tipo_documento_id' => $tipoDocumentoId,
        ];
    }

    private function persistirPersonaConUsuario(
        array $resultadoDuplicados,
        array $data,
        PersonaImport $import,
        array $faltantes,
        int &$missingContact
    ): void {
        DB::transaction(function () use ($resultadoDuplicados, $data, $import, $faltantes, &$missingContact) {
            // Validación de seguridad: asegurar que el tipo de documento esté presente
            $tipoDocumentoId = $resultadoDuplicados['tipo_documento_id'] ?? null;
            if (!$tipoDocumentoId) {
                throw new MissingDocumentTypeException($data['tipo_documento'] ?? null);
            }

            $datosPersona = [
                'tipo_documento' => $tipoDocumentoId,
                'numero_documento' => $data['numero_documento'],
                'primer_nombre' => $data['primer_nombre'],
                'segundo_nombre' => Arr::get($data, 'segundo_nombre'),
                'primer_apellido' => Arr::get($data, 'primer_apellido'),
                'segundo_apellido' => Arr::get($data, 'segundo_apellido'),
                'telefono' => Arr::get($data, 'telefono'),
                'celular' => Arr::get($data, 'celular'),
                'email' => Arr::get($data, 'email'),
                'status' => 1,
            ];

            // Usar PersonaService para crear la persona
            $persona = $this->personaService->crearSinUsuario($datosPersona, $import->user_id);

            // Ya no se crea usuario automáticamente durante la importación

            if (! in_array(true, $faltantes, true)) {
                return;
            }

            PersonaContactAlert::create([
                'persona_id' => $persona->id,
                'persona_import_id' => $import->id,
                'missing_email' => $faltantes['missing_email'],
                'missing_celular' => $faltantes['missing_celular'],
                'missing_telefono' => $faltantes['missing_telefono'],
                'raw_payload' => $data,
            ]);

            $missingContact++;
        });
    }

    private function registrarIssue(
        PersonaImport $import,
        int $rowNumber,
        string $issueType,
        ?string $documento,
        ?string $email,
        ?string $celular,
        array $raw
    ): void {
        PersonaImportIssue::create([
            'persona_import_id' => $import->id,
            'row_number' => $rowNumber,
            'issue_type' => $issueType,
            'numero_documento' => $documento,
            'email' => $email,
            'celular' => $celular,
            'raw_payload' => $raw,
        ]);
    }

    private function resolverTipoDocumentoId(?string $valor): ?int
    {
        if (!$valor) {
            return null;
        }

        $normalizado = $this->normalizarTextoSinTildes(trim($valor));

        $clave = $this->documentAliasMap[$normalizado] ?? $normalizado;

        $tipoDocumentoId = $this->documentoCache[$clave] ?? null;

        // Log si no se encuentra el tipo de documento para diagnóstico
        if (!$tipoDocumentoId && !empty($clave)) {
            Log::warning('Tipo de documento no encontrado en cache', [
                'valor_original' => $valor,
                'valor_normalizado' => $normalizado,
                'clave_buscada' => $clave,
                'cache_keys' => array_keys($this->documentoCache),
                'cache_values' => $this->documentoCache,
            ]);
        }

        return $tipoDocumentoId;
    }


    /**
     * Procesa el archivo en chunks para optimizar memoria
     * Retorna el total de filas válidas procesadas
     */
    private function procesarChunks(
        $reader,
        string $rutaArchivo,
        int $highestRow,
        $filter,
        PersonaImport $import
    ): int {
        $processed = 0;
        $success = 0;
        $duplicates = 0;
        $missingContact = 0;

        for ($startRow = 1; $startRow <= $highestRow; $startRow += self::CHUNK_SIZE) {
            $chunkRecords = $this->leerChunk($reader, $rutaArchivo, $startRow, $filter);

            if (empty($chunkRecords)) {
                continue;
            }

            $existsCaches = $this->consultarExistencias($chunkRecords);
            $resultados = $this->procesarRegistrosChunk($chunkRecords, $existsCaches, $import);

            $processed += $resultados['processed'];
            $success += $resultados['success'];
            $duplicates += $resultados['duplicates'];
            $missingContact += $resultados['missingContact'];

            $import->update([
                'processed_rows' => $processed,
                'success_count' => $success,
                'duplicate_count' => $duplicates,
                'missing_contact_count' => $missingContact,
            ]);
        }

        return $processed;
    }

    /**
     * Lee un chunk del archivo Excel
     */
    private function leerChunk($reader, string $rutaArchivo, int $startRow, $filter): array
    {
        $filter->setRows($startRow, self::CHUNK_SIZE);
        $chunkSpreadsheet = $reader->load($rutaArchivo);
        $sheet = $chunkSpreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        $chunkRecords = [];
        $currentRowNumber = $startRow;

        foreach ($rows as $columns) {
            $rowNumber = $currentRowNumber++;

            if ($rowNumber === 1 && empty($this->headerMap)) {
                $this->headerMap = $this->resolverEncabezados($columns);

                if (empty($this->headerMap)) {
                    throw new ImportHeaderMismatchException();
                }

                continue;
            }

            $mapped = $this->mapearFila($columns);

            if ($this->filaVacia($mapped)) {
                continue;
            }

            $chunkRecords[] = [
                'row_number' => $rowNumber,
                'data' => $mapped,
                'raw' => $columns,
            ];
        }

        // Liberar recursos del chunk inmediatamente
        unset($sheet, $rows);
        $chunkSpreadsheet->disconnectWorksheets();
        unset($chunkSpreadsheet);

        return $chunkRecords;
    }

    /**
     * Procesa los registros de un chunk
     */
    private function procesarRegistrosChunk(array $chunkRecords, array $existsCaches, PersonaImport $import): array
    {
        $processed = 0;
        $success = 0;
        $duplicates = 0;
        $missingContact = 0;

        foreach ($chunkRecords as $record) {
            $rowNumber = $record['row_number'];
            $data = $record['data'];
            $raw = $record['raw'];

            $processed++;

            $resultadoDuplicados = $this->validarDuplicados($data, $existsCaches, $import, $rowNumber, $raw);

            if ($resultadoDuplicados['es_duplicado']) {
                $duplicates++;
                continue;
            }

            $resultado = $this->procesarRegistro($data, $resultadoDuplicados, $import, $rowNumber);
            if ($resultado['success']) {
                $success++;
            } else {
                $duplicates++;
            }
            $missingContact += $resultado['missingContact'];
        }

        return [
            'processed' => $processed,
            'success' => $success,
            'duplicates' => $duplicates,
            'missingContact' => $missingContact,
        ];
    }

    /**
     * Procesa un registro individual
     */
    private function procesarRegistro(
        array $data,
        array $resultadoDuplicados,
        PersonaImport $import,
        int $rowNumber
    ): array {
        $missingContact = 0;

        try {
            $faltantes = [
                'missing_email' => empty($data['email']),
                'missing_celular' => empty($data['celular']),
                'missing_telefono' => empty($data['telefono']),
            ];

            $this->persistirPersonaConUsuario(
                $resultadoDuplicados,
                $data,
                $import,
                $faltantes,
                $missingContact
            );

            return ['success' => true, 'missingContact' => $missingContact];
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage();
            
            // Intentar reintento si hay campos duplicados no críticos
            if (str_contains($errorMessage, 'Integrity constraint violation')) {
                $resultado = $this->intentarReintento(
                    $e,
                    $data,
                    $resultadoDuplicados,
                    $import,
                    $rowNumber,
                    $missingContact
                );
                
                if ($resultado !== null) {
                    return $resultado;
                }
            }
            
            // Error no recuperable - registrar issue
            return $this->registrarErrorNoRecuperable($e, $data, $import, $rowNumber);
        }
    }

    /**
     * Intenta reintentar el guardado omitiendo campos duplicados no críticos
     */
    private function intentarReintento(
        \Throwable $e,
        array $data,
        array $resultadoDuplicados,
        PersonaImport $import,
        int $rowNumber,
        int &$missingContact
    ): ?array {
        $errorMessage = $e->getMessage();
        $camposDuplicados = $this->detectarCamposDuplicados($errorMessage, $data);
        
        if (empty($camposDuplicados)) {
            return null;
        }

        try {
            $faltantes = [
                'missing_email' => empty($data['email']),
                'missing_celular' => empty($data['celular']),
                'missing_telefono' => empty($data['telefono']),
            ];

            $this->persistirPersonaConUsuario(
                $resultadoDuplicados,
                $data,
                $import,
                $faltantes,
                $missingContact
            );

            // Registrar issue informativo (no es error fatal)
            $issueType = 'partial_import_' . implode('_', $camposDuplicados);
            $mensaje = 'Persona creada omitiendo campos duplicados: '
                . implode(', ', $camposDuplicados);

            PersonaImportIssue::create([
                'persona_import_id' => $import->id,
                'row_number' => $rowNumber,
                'issue_type' => $issueType,
                'numero_documento' => Arr::get($data, 'numero_documento'),
                'email' => Arr::get($data, 'email'),
                'celular' => Arr::get($data, 'celular'),
                'error_message' => $mensaje,
                'raw_payload' => $data,
            ]);

            return ['success' => true, 'missingContact' => $missingContact];
        } catch (\Throwable $retryError) {
            Log::error('Error en reintento de importación', [
                'import_id' => $import->id,
                'row' => $rowNumber,
                'error' => $retryError->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Detecta qué campos están duplicados y los limpia del array de datos
     */
    private function detectarCamposDuplicados(string $errorMessage, array &$data): array
    {
        $camposDuplicados = [];

        if (str_contains($errorMessage, 'personas_email_unique') ||
            (str_contains($errorMessage, self::DUPLICATE_ENTRY_TEXT) &&
             str_contains($errorMessage, 'email'))) {
            $camposDuplicados[] = 'email';
            $data['email'] = null;
        }

        if (str_contains($errorMessage, 'personas_celular_unique') ||
            (str_contains($errorMessage, self::DUPLICATE_ENTRY_TEXT) &&
             str_contains($errorMessage, 'celular'))) {
            $camposDuplicados[] = 'celular';
            $data['celular'] = null;
        }

        if (str_contains($errorMessage, 'personas_telefono_unique') ||
            (str_contains($errorMessage, self::DUPLICATE_ENTRY_TEXT) &&
             str_contains($errorMessage, 'telefono'))) {
            $camposDuplicados[] = 'telefono';
            $data['telefono'] = null;
        }

        return $camposDuplicados;
    }

    /**
     * Registra un error no recuperable como issue
     */
    private function registrarErrorNoRecuperable(
        \Throwable $e,
        array $data,
        PersonaImport $import,
        int $rowNumber
    ): array {
        Log::error('Error guardando persona en importación', [
            'import_id' => $import->id,
            'row' => $rowNumber,
            'error' => $e->getMessage(),
        ]);

        $issueType = $this->detectarTipoError($e);

        PersonaImportIssue::create([
            'persona_import_id' => $import->id,
            'row_number' => $rowNumber,
            'issue_type' => $issueType,
            'numero_documento' => Arr::get($data, 'numero_documento'),
            'email' => Arr::get($data, 'email'),
            'celular' => Arr::get($data, 'celular'),
            'error_message' => $e->getMessage(),
            'raw_payload' => $data,
        ]);

        return ['success' => false, 'missingContact' => 0];
    }


    /**
     * Detecta el tipo de error de duplicado basado en el mensaje de excepción
     */
    private function detectarTipoError(\Throwable $e): string
    {
        $errorMessage = $e->getMessage();
        $tipoError = 'persist_error';

        if (str_contains($errorMessage, 'Integrity constraint violation')) {
            // Mapeo de patrones a tipos de error
            $patrones = [
                'duplicate_email_existing' => ['personas_email_unique', 'email'],
                'duplicate_document_existing' => ['personas_numero_documento_unique', 'numero_documento'],
                'duplicate_celular_existing' => ['personas_celular_unique', 'celular'],
                'duplicate_telefono_existing' => ['personas_telefono_unique', 'telefono'],
            ];

            foreach ($patrones as $tipo => $patronesBusqueda) {
                foreach ($patronesBusqueda as $patron) {
                    if (str_contains($errorMessage, $patron)) {
                        $tipoError = $tipo;
                        break 2;
                    }
                }
            }

            // Error genérico de duplicado si no se encontró un tipo específico
            if ($tipoError === 'persist_error' && str_contains($errorMessage, self::DUPLICATE_ENTRY_TEXT)) {
                $tipoError = 'duplicate_generic';
            }
        }

        return $tipoError;
    }
}
