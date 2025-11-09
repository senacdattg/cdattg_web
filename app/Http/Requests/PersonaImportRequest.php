<?php

namespace App\Http\Requests;

use App\Configuration\UploadLimits;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class PersonaImportRequest extends FormRequest
{
    /**
     * Determina si el usuario está autorizado para realizar esta petición.
     */
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Obtiene las reglas de validación aplicables a la petición.
     */
    public function rules(): array
    {
        return [
            'archivo_excel' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv',
                'max:' . UploadLimits::IMPORT_FILE_SIZE_KB,
                function ($attribute, $value, $fail) {
                    $this->validateFileIntegrity($value, $fail);
                },
            ],
        ];
    }

    /**
     * Obtiene los mensajes de error personalizados.
     */
    public function messages(): array
    {
        $maxSizeMB = UploadLimits::IMPORT_FILE_SIZE_MB;

        return [
            'archivo_excel.required' => 'Debes seleccionar un archivo para importar.',
            'archivo_excel.file' => 'El archivo proporcionado no es válido.',
            'archivo_excel.mimes' => 'El archivo debe ser de tipo Excel (.xlsx, .xls) o CSV.',
            'archivo_excel.max' => "El archivo no debe superar los {$maxSizeMB}MB.",
        ];
    }

    /**
     * Validaciones adicionales de integridad del archivo.
     */
    private function validateFileIntegrity($file, $fail): void
    {
        if (!$file || !$file->isValid()) {
            $fail('El archivo no es válido o está corrupto.');
            return;
        }

        // Validar que el tamaño real del archivo coincida con lo declarado
        $realSize = $file->getSize();

        if (!UploadLimits::isWithinLimit($realSize, UploadLimits::IMPORT_FILE_SIZE_BYTES)) {
            $sizeMB = round($realSize / 1024 / 1024, 2);
            $maxMB = UploadLimits::IMPORT_FILE_SIZE_MB;
            $fail("El tamaño real del archivo ({$sizeMB}MB) excede el límite permitido de {$maxMB}MB.");
            return;
        }

        // Validar que el archivo tenga contenido
        if ($realSize === 0) {
            $fail('El archivo está vacío.');
            return;
        }

        // Validar extensión contra el mime type real usando la configuración centralizada
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();

        if (!UploadLimits::isValidExcelMimeType($extension, $mimeType)) {
            $fail("El tipo MIME del archivo ({$mimeType}) no coincide con la extensión ({$extension}).");
        }
    }

    /**
     * Maneja una validación fallida.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Error de validación en el archivo.',
                'errors' => $validator->errors(),
            ], Response::HTTP_UNPROCESSABLE_ENTITY)
        );
    }

    /**
     * Prepara los datos para la validación.
     */
    protected function prepareForValidation(): void
    {
        // Validar Content-Length si está presente
        $contentLength = $this->header('Content-Length');

        if ($contentLength !== null && (int) $contentLength > UploadLimits::IMPORT_CONTENT_LENGTH_BYTES) {
            $maxSize = UploadLimits::formatBytes(UploadLimits::IMPORT_CONTENT_LENGTH_BYTES);

            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => "El tamaño de la petición excede el límite permitido de {$maxSize}.",
                    'max_size_bytes' => UploadLimits::IMPORT_CONTENT_LENGTH_BYTES,
                    'request_size_bytes' => (int) $contentLength,
                ], Response::HTTP_REQUEST_ENTITY_TOO_LARGE)
            );
        }
    }
}
