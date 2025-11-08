<?php

namespace App\Services;

use App\Jobs\ProcessPersonaImportJob;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\PersonaContactAlert;
use App\Models\PersonaImport;
use App\Models\PersonaImportIssue;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

class PersonaImportService
{
    private const CHUNK_SIZE = 750;

    private array $headerMap = [];
    private array $documentSeen = [];
    private array $emailSeen = [];
    private array $celularSeen = [];
    private array $telefonoSeen = [];
    private array $documentAliasMap = [
        'CC' => 'CEDULA DE CIUDADANIA',
        'CEDULA DE CIUDADANIA' => 'CEDULA DE CIUDADANIA',
        'CEDULA CIUDADANIA' => 'CEDULA DE CIUDADANIA',
        'CEDULA' => 'CEDULA DE CIUDADANIA',
        'C.C.' => 'CEDULA DE CIUDADANIA',
        'CE' => 'CEDULA DE EXTRANJERIA',
        'C.E.' => 'CEDULA DE EXTRANJERIA',
        'CEDULA DE EXTRANJERIA' => 'CEDULA DE EXTRANJERIA',
        'PASAPORTE' => 'PASAPORTE',
        'PA' => 'PASAPORTE',
        'TI' => 'TARJETA DE IDENTIDAD',
        'T.I.' => 'TARJETA DE IDENTIDAD',
        'TARJETA DE IDENTIDAD' => 'TARJETA DE IDENTIDAD',
        'RC' => 'REGISTRO CIVIL',
        'R.C.' => 'REGISTRO CIVIL',
        'REGISTRO CIVIL' => 'REGISTRO CIVIL',
        'SIN DOCUMENTO' => 'SIN IDENTIFICACION',
        'SD' => 'SIN IDENTIFICACION',
        'S/D' => 'SIN IDENTIFICACION',
        'SIN IDENTIFICACION' => 'SIN IDENTIFICACION',
    ];

    private array $documentoCache = [];

    public function __construct()
    {
        $this->warmDocumentoCache();
    }

    public function iniciarImportacion(UploadedFile $file, int $userId): PersonaImport
    {
        $storedPath = $file->store('imports/personas', 'local');

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
        $import->update(['status' => 'processing', 'processed_rows' => 0, 'success_count' => 0, 'duplicate_count' => 0, 'missing_contact_count' => 0]);

        $rutaArchivo = Storage::disk($import->disk)->path($import->path);

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
                        throw new \RuntimeException('El encabezado del archivo no coincide con el formato esperado.');
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

                try {
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

                    $faltantes = [
                        'missing_email' => empty($data['email']),
                        'missing_celular' => empty($data['celular']),
                        'missing_telefono' => empty($data['telefono']),
                    ];

                    if (in_array(true, $faltantes, true)) {
                        PersonaContactAlert::create([
                            'persona_id' => $persona->id,
                            'persona_import_id' => $import->id,
                            'missing_email' => $faltantes['missing_email'],
                            'missing_celular' => $faltantes['missing_celular'],
                            'missing_telefono' => $faltantes['missing_telefono'],
                            'raw_payload' => $data,
                        ]);

                        $missingContact++;
                    }

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
                        'raw_payload' => $data,
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
        $names = array_unique(array_values($this->documentAliasMap));
        $this->documentoCache = Parametro::whereIn('name', $names)
            ->pluck('id', 'name')
            ->mapWithKeys(function ($id, $name) {
                return [Str::upper($name) => (int) $id];
            })
            ->toArray();
    }

    private function resolverEncabezados(array $row): array
    {
        $map = [];

        $headerTargets = [
            'tipo_documento' => ['tipo de documento', 'tipodocumento'],
            'numero_documento' => ['numero de documento', 'número de documento', 'numero documento', 'documento', 'documento identidad'],
            'primer_nombre' => ['primer nombre', 'nombre', 'nombre1'],
            'segundo_nombre' => ['segundo nombre', 'nombre2'],
            'primer_apellido' => ['primer apellido', 'apellido', 'apellido1'],
            'segundo_apellido' => ['segundo apellido', 'apellido2'],
            'email' => ['correo electronico', 'correo electrónico', 'correo', 'email'],
            'celular' => ['celular', 'telefono celular', 'teléfono celular', 'movil', 'móvil'],
            'telefono' => ['telefono', 'teléfono', 'telefono fijo', 'teléfono fijo'],
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
        $documentos = array_values(array_unique(array_filter(array_map(fn($item) => $item['data']['numero_documento'] ?? null, $records))));
        $emails = array_values(array_unique(array_filter(array_map(fn($item) => $item['data']['email'] ?? null, $records))));
        $celulares = array_values(array_unique(array_filter(array_map(fn($item) => $item['data']['celular'] ?? null, $records))));
        $telefonos = array_values(array_unique(array_filter(array_map(fn($item) => $item['data']['telefono'] ?? null, $records))));

        return [
            'documentos' => empty($documentos) ? [] : Persona::whereIn('numero_documento', $documentos)->pluck('numero_documento')->map(fn($doc) => (string) $doc)->toArray(),
            'emails' => empty($emails) ? [] : Persona::whereIn('email', $emails)->pluck('email')->map(fn($email) => Str::lower($email))->toArray(),
            'celulares' => empty($celulares) ? [] : Persona::whereIn('celular', $celulares)->pluck('celular')->map(fn($cel) => $cel)->toArray(),
            'telefonos' => empty($telefonos) ? [] : Persona::whereIn('telefono', $telefonos)->pluck('telefono')->map(fn($tel) => $tel)->toArray(),
        ];
    }

    private function validarDuplicados(array $data, array $existsCaches, PersonaImport $import, int $rowNumber, array $raw): array
    {
        $esDuplicado = false;
        $tipoDocumentoId = $data['tipo_documento_id'] ?? null;

        $numeroDocumento = $data['numero_documento'] ?? null;
        $email = $data['email'] ?? null;
        $celular = $data['celular'] ?? null;
        $telefono = $data['telefono'] ?? null;

        if (!$numeroDocumento) {
            $esDuplicado = true;
            $this->registrarIssue($import, $rowNumber, 'missing_document', $numeroDocumento, $email, $celular, $raw);
            return ['es_duplicado' => true, 'tipo_documento_id' => $tipoDocumentoId];
        }

        if (empty($data['primer_nombre']) || empty($data['primer_apellido'])) {
            $esDuplicado = true;
            $this->registrarIssue($import, $rowNumber, 'missing_required_fields', $numeroDocumento, $email, $celular, $raw);
            return ['es_duplicado' => true, 'tipo_documento_id' => $tipoDocumentoId];
        }

        if (isset($this->documentSeen[$numeroDocumento])) {
            $esDuplicado = true;
            $this->registrarIssue($import, $rowNumber, 'duplicate_document_in_file', $numeroDocumento, $email, $celular, $raw);
        } elseif (in_array($numeroDocumento, $existsCaches['documentos'], true)) {
            $esDuplicado = true;
            $this->registrarIssue($import, $rowNumber, 'duplicate_document_existing', $numeroDocumento, $email, $celular, $raw);
        } else {
            $this->documentSeen[$numeroDocumento] = true;
        }

        if ($email) {
            $emailKey = Str::lower($email);
            if (isset($this->emailSeen[$emailKey])) {
                $esDuplicado = true;
                $this->registrarIssue($import, $rowNumber, 'duplicate_email_in_file', $numeroDocumento, $email, $celular, $raw);
            } elseif (in_array($emailKey, $existsCaches['emails'], true)) {
                $esDuplicado = true;
                $this->registrarIssue($import, $rowNumber, 'duplicate_email_existing', $numeroDocumento, $email, $celular, $raw);
            } else {
                $this->emailSeen[$emailKey] = true;
            }
        }

        if ($celular) {
            if (isset($this->celularSeen[$celular])) {
                $esDuplicado = true;
                $this->registrarIssue($import, $rowNumber, 'duplicate_celular_in_file', $numeroDocumento, $email, $celular, $raw);
            } elseif (in_array($celular, $existsCaches['celulares'], true)) {
                $esDuplicado = true;
                $this->registrarIssue($import, $rowNumber, 'duplicate_celular_existing', $numeroDocumento, $email, $celular, $raw);
            } else {
                $this->celularSeen[$celular] = true;
            }
        }

        if ($telefono) {
            if (isset($this->telefonoSeen[$telefono])) {
                $esDuplicado = true;
                $this->registrarIssue($import, $rowNumber, 'duplicate_telefono_in_file', $numeroDocumento, $email, $celular, $raw);
            } elseif (in_array($telefono, $existsCaches['telefonos'], true)) {
                $esDuplicado = true;
                $this->registrarIssue($import, $rowNumber, 'duplicate_telefono_existing', $numeroDocumento, $email, $celular, $raw);
            } else {
                $this->telefonoSeen[$telefono] = true;
            }
        }

        return [
            'es_duplicado' => $esDuplicado,
            'tipo_documento_id' => $tipoDocumentoId,
        ];
    }

    private function registrarIssue(PersonaImport $import, int $rowNumber, string $issueType, ?string $documento, ?string $email, ?string $celular, array $raw): void
    {
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
