<?php

namespace App\Services;

use App\Exceptions\ImportFileNotFoundException;
use App\Exceptions\ImportHeaderMismatchException;
use App\Jobs\ProcessPersonaImportJob;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\PersonaContactAlert;
use App\Models\PersonaImport;
use App\Models\PersonaImportIssue;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class PersonaImportService
{
    private const CHUNK_SIZE = 250;
    private const EMAIL_PLACEHOLDER_DOMAIN = 'visitante.local';

    private const DOC_CEDULA_CIUDADANIA = 'CEDULA DE CIUDADANIA';
    private const DOC_CEDULA_EXTRANJERIA = 'CEDULA DE EXTRANJERIA';
    private const DOC_PASAPORTE = 'PASAPORTE';
    private const DOC_PERMISO_PROTECCION = 'PERMISO POR PROTECCION TEMPORAL';
    private const DOC_TARJETA_IDENTIDAD = 'TARJETA DE IDENTIDAD';
    private const DOC_REGISTRO_CIVIL = 'REGISTRO CIVIL';
    private const DOC_SIN_IDENTIFICACION = 'SIN IDENTIFICACION';

    private array $headerMap = [];
    private array $documentSeen = [];
    private array $documentAliasMap = [
        'CC' => self::DOC_CEDULA_CIUDADANIA,
        'CEDULA DE CIUDADANIA' => self::DOC_CEDULA_CIUDADANIA,
        'CEDULA CIUDADANIA' => self::DOC_CEDULA_CIUDADANIA,
        'CEDULA' => self::DOC_CEDULA_CIUDADANIA,
        'C.C.' => self::DOC_CEDULA_CIUDADANIA,
        'CE' => self::DOC_CEDULA_EXTRANJERIA,
        'C.E.' => self::DOC_CEDULA_EXTRANJERIA,
        'CEDULA DE EXTRANJERIA' => self::DOC_CEDULA_EXTRANJERIA,
        'PASAPORTE' => self::DOC_PASAPORTE,
        'PA' => self::DOC_PASAPORTE,
        'PPT' => self::DOC_PERMISO_PROTECCION,
        'PERMISO POR PROTECCION TEMPORAL' => self::DOC_PERMISO_PROTECCION,
        'PERMISO POR PROTECCIÓN TEMPORAL' => self::DOC_PERMISO_PROTECCION,
        'PERMISO PROTECCION TEMPORAL' => self::DOC_PERMISO_PROTECCION,
        'TI' => self::DOC_TARJETA_IDENTIDAD,
        'T.I.' => self::DOC_TARJETA_IDENTIDAD,
        'TARJETA DE IDENTIDAD' => self::DOC_TARJETA_IDENTIDAD,
        'RC' => self::DOC_REGISTRO_CIVIL,
        'R.C.' => self::DOC_REGISTRO_CIVIL,
        'REGISTRO CIVIL' => self::DOC_REGISTRO_CIVIL,
        'SIN DOCUMENTO' => self::DOC_SIN_IDENTIFICACION,
        'SIN' => self::DOC_SIN_IDENTIFICACION,
        'SD' => self::DOC_SIN_IDENTIFICACION,
        'S/D' => self::DOC_SIN_IDENTIFICACION,
        'SIN IDENTIFICACION' => self::DOC_SIN_IDENTIFICACION,
    ];

    private array $documentoCache = [];

    public function __construct()
    {
        $this->warmDocumentoCache();
    }

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
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);

        if ($highestRow <= 1) {
            $import->update([
                'total_rows' => 0,
                'status' => 'completed',
            ]);
            return;
        }

        $import->update([
            'total_rows' => $highestRow - 1,
        ]);

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

        $processed = 0;
        $success = 0;
        $duplicates = 0;
        $missingContact = 0;

        for ($startRow = 1; $startRow <= $highestRow; $startRow += self::CHUNK_SIZE) {
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

            $chunkSpreadsheet->disconnectWorksheets();
            unset($chunkSpreadsheet);

            if (empty($chunkRecords)) {
                continue;
            }

            $existsCaches = $this->consultarExistencias($chunkRecords);

            $chunkCounter = 0;
            foreach ($chunkRecords as $record) {
                $rowNumber = $record['row_number'];
                $data = $record['data'];
                $raw = $record['raw'];

                $processed++;
                $chunkCounter++;

                $resultadoDuplicados = $this->validarDuplicados($data, $existsCaches, $import, $rowNumber, $raw);

                if ($resultadoDuplicados['es_duplicado']) {
                    $duplicates++;
                    continue;
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

                    $success++;
                } catch (\Throwable $e) {
                    Log::error('Error guardando persona en importación', [
                        'import_id' => $import->id,
                        'row' => $rowNumber,
                        'error' => $e->getMessage(),
                    ]);

                    $duplicates++;

                    PersonaImportIssue::create([
                        'persona_import_id' => $import->id,
                        'row_number' => $rowNumber,
                        'issue_type' => 'persist_error',
                        'numero_documento' => Arr::get($data, 'numero_documento'),
                        'email' => Arr::get($data, 'email'),
                        'celular' => Arr::get($data, 'celular'),
                        'error_message' => $e->getMessage(),
                        'raw_payload' => $data,
                    ]);
                }

                if ($chunkCounter % 50 === 0) {
                    $import->update([
                        'processed_rows' => $processed,
                        'success_count' => $success,
                        'duplicate_count' => $duplicates,
                        'missing_contact_count' => $missingContact,
                    ]);
                }
            }

            $import->update([
                'processed_rows' => $processed,
                'success_count' => $success,
                'duplicate_count' => $duplicates,
                'missing_contact_count' => $missingContact,
            ]);
        }

        $import->update([
            'status' => 'completed',
        ]);
    }

    private function warmDocumentoCache(): void
    {
        if (! Schema::hasTable('parametros')) {
            Log::warning('La tabla "parametros" no existe; los tipos de documento se resolverán como null');
            $this->documentoCache = [];
            return;
        }

        $names = array_unique(array_values($this->documentAliasMap));
        $this->documentoCache = Parametro::whereIn('name', $names)
            ->pluck('id', 'name')
            ->mapWithKeys(function ($id, $name) {
                return [Str::upper($name) => (int) $id];
            })
            ->toArray();

        if (empty($this->documentoCache)) {
            Log::warning('No se encontraron tipos de documento en la base de datos');
        }
    }

    private function resolverEncabezados(array $row): array
    {
        $map = [];

        $headerTargets = [
            'tipo_documento' => [
                'tipo de documento',
                'tipodocumento',
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
            $normalized = $this->normalizarTexto($value ?? '');

            foreach ($headerTargets as $field => $targets) {
                if (in_array($normalized, $targets, true)) {
                    $map[$field] = $column;
                    break;
                }
            }
        }

        $requeridos = ['tipo_documento', 'numero_documento', 'primer_nombre', 'primer_apellido'];

        foreach ($requeridos as $campo) {
            if (!array_key_exists($campo, $map)) {
                return [];
            }
        }

        return $map;
    }

    private function mapearFila(array $row): array
    {
        $datos = [];

        foreach ($this->headerMap as $field => $column) {
            $valor = $row[$column] ?? null;
            $datos[$field] = is_string($valor) ? trim($valor) : $valor;
        }

        $datos['numero_documento'] = $this->limpiarNumeroDocumento($datos['numero_documento'] ?? '');
        $datos['primer_nombre'] = $datos['primer_nombre'] ? Str::upper($datos['primer_nombre']) : null;
        $datos['segundo_nombre'] = $datos['segundo_nombre'] ? Str::upper($datos['segundo_nombre']) : null;
        $datos['primer_apellido'] = $datos['primer_apellido'] ? Str::upper($datos['primer_apellido']) : null;
        $datos['segundo_apellido'] = $datos['segundo_apellido'] ? Str::upper($datos['segundo_apellido']) : null;
        $datos['email'] = $this->normalizarEmail($datos['email'] ?? null);
        $datos['celular'] = $this->normalizarTelefono($datos['celular'] ?? null);
        $datos['telefono'] = $this->normalizarTelefono($datos['telefono'] ?? null);
        $datos['tipo_documento_id'] = $this->resolverTipoDocumentoId($datos['tipo_documento'] ?? null);

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
        if (! $numeroDocumento) {
            $esDuplicado = true;
            $this->registrarIssue(
                $import,
                $rowNumber,
                'missing_document',
                $numeroDocumento,
                $email,
                $celular,
                $raw
            );

            return [
                'es_duplicado' => true,
                'tipo_documento_id' => $tipoDocumentoId,
            ];
        }

        if (empty($data['primer_nombre']) || empty($data['primer_apellido'])) {
            $esDuplicado = true;
            $this->registrarIssue(
                $import,
                $rowNumber,
                'missing_required_fields',
                $numeroDocumento,
                $email,
                $celular,
                $raw
            );

            return [
                'es_duplicado' => true,
                'tipo_documento_id' => $tipoDocumentoId,
            ];
        }

        if (isset($this->documentSeen[$numeroDocumento])) {
            $esDuplicado = true;
            $this->registrarIssue(
                $import,
                $rowNumber,
                'duplicate_document_in_file',
                $numeroDocumento,
                $email,
                $celular,
                $raw
            );
        } elseif (in_array($numeroDocumento, $existsCaches['documentos'], true)) {
            $esDuplicado = true;
            $this->registrarIssue(
                $import,
                $rowNumber,
                'duplicate_document_existing',
                $numeroDocumento,
                $email,
                $celular,
                $raw
            );
        } else {
            $this->documentSeen[$numeroDocumento] = true;
        }

        return [
            'es_duplicado' => $esDuplicado,
            'tipo_documento_id' => $tipoDocumentoId,
        ];
    }

    private function crearUsuarioVisitante(Persona $persona): void
    {
        $email = $this->resolverEmailUsuario($persona);

        $usuarioRelacionado = $persona->user;
        if ($usuarioRelacionado) {
            if ($usuarioRelacionado->email !== $email) {
                $correoOcupado = User::where('email', $email)
                    ->where('id', '!=', $usuarioRelacionado->id)
                    ->exists();

                if (! $correoOcupado) {
                    $usuarioRelacionado->forceFill(['email' => $email])->save();
                } else {
                    $this->asegurarRolVisitante($usuarioRelacionado);
                    return;
                }
            }

            $this->asegurarRolVisitante($usuarioRelacionado);
            return;
        }

        $usuarioPorEmail = User::where('email', $email)->first();
        if ($usuarioPorEmail) {
            if ($usuarioPorEmail->persona_id !== $persona->id) {
                $usuarioPorEmail->forceFill(['persona_id' => $persona->id])->save();
            }
            $this->asegurarRolVisitante($usuarioPorEmail);
            return;
        }

        $user = User::create([
            'email' => $email,
            'password' => Hash::make($this->resolverClaveInicial($persona)),
            'status' => 1,
            'persona_id' => $persona->id,
        ]);

        $this->asegurarRolVisitante($user);
    }

    /**
     * Obtiene el email aplicable al usuario asociado a la persona.
     * Genera un correo placeholder en caso de que la persona no tenga correo.
     */
    private function resolverEmailUsuario(Persona $persona): string
    {
        $emailOriginal = $persona->getRawOriginal('email');
        if ($emailOriginal) {
            return Str::lower(trim($emailOriginal));
        }

        return sprintf('persona-%d@%s', $persona->id, self::EMAIL_PLACEHOLDER_DOMAIN);
    }

    /**
     * Determina la clave inicial para usuarios derivados de importación.
     * Priorizamos el número de documento para mantener fricción baja.
     */
    private function resolverClaveInicial(Persona $persona): string
    {
        $numeroDocumento = $persona->numero_documento;

        if (is_string($numeroDocumento) && $numeroDocumento !== '') {
            return $numeroDocumento;
        }

        return Str::random(12);
    }

    private function asegurarRolVisitante(User $user): void
    {
        if (! $user->hasRole('VISITANTE')) {
            $user->assignRole('VISITANTE');
        }
    }

    private function persistirPersonaConUsuario(
        array $resultadoDuplicados,
        array $data,
        PersonaImport $import,
        array $faltantes,
        int &$missingContact
    ): void {
        DB::transaction(function () use ($resultadoDuplicados, $data, $import, $faltantes, &$missingContact) {
            $persona = Persona::create([
                'tipo_documento' => $resultadoDuplicados['tipo_documento_id'],
                'numero_documento' => $data['numero_documento'],
                'primer_nombre' => $data['primer_nombre'],
                'segundo_nombre' => Arr::get($data, 'segundo_nombre'),
                'primer_apellido' => Arr::get($data, 'primer_apellido'),
                'segundo_apellido' => Arr::get($data, 'segundo_apellido'),
                'telefono' => Arr::get($data, 'telefono'),
                'celular' => Arr::get($data, 'celular'),
                'email' => Arr::get($data, 'email'),
                'status' => 1,
                'user_create_id' => $import->user_id,
                'user_edit_id' => $import->user_id,
            ]);

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

        $normalizado = Str::upper(Str::ascii(trim($valor)));

        $clave = $this->documentAliasMap[$normalizado] ?? $normalizado;

        return $this->documentoCache[$clave] ?? null;
    }

    private function normalizarTexto(?string $texto): string
    {
        return Str::lower(Str::ascii(trim($texto ?? '')));
    }

    private function limpiarNumeroDocumento(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $limpio = preg_replace('/[^0-9A-Z]/', '', Str::upper(Str::ascii($valor)));

        return $limpio ?: null;
    }

    private function normalizarEmail(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }

        $correo = Str::lower(trim($valor));

        return filter_var($correo, FILTER_VALIDATE_EMAIL) ? $correo : null;
    }

    private function normalizarTelefono(?string $valor): ?string
    {
        if (!$valor) {
            return null;
        }

        $soloDigitos = preg_replace('/\D/', '', $valor);

        return $soloDigitos ?: null;
    }
}
