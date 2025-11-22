<?php

namespace App\Configuration;

/**
 * Configuración centralizada de límites de carga de archivos.
 *
 * Esta clase define los límites de tamaño para diferentes tipos de cargas
 * y proporciona métodos para validarlos de manera consistente en toda la aplicación.
 *
 * IMPORTANTE: Estos límites deben estar sincronizados con la configuración de PHP:
 * - upload_max_filesize >= 8M
 * - post_max_size >= 8M
 * - memory_limit >= 128M
 * - max_execution_time >= 300 (5 minutos)
 */
final class UploadLimits
{
    /**
     * Tamaño máximo para importación de archivos Excel (8MB).
     */
    public const IMPORT_FILE_SIZE_KB = 25600;
    public const IMPORT_FILE_SIZE_BYTES = self::IMPORT_FILE_SIZE_KB * 1024;
    public const IMPORT_FILE_SIZE_MB = self::IMPORT_FILE_SIZE_KB / 1024;

    /**
     * Tamaño máximo del contenido HTTP para solicitudes estándar (2MB).
     * Úsese como valor base en middleware globales para evitar DoS.
     */
    public const GENERAL_CONTENT_LENGTH_BYTES = 2 * 1024 * 1024; // 2MB

    /**
     * Tamaño máximo del contenido HTTP para importaciones (8MB).
     * Mantenerlo alineado con el límite del archivo evita sobrecargas del buffer de entrada.
     */
    public const IMPORT_CONTENT_LENGTH_BYTES = self::IMPORT_FILE_SIZE_KB * 1024;

    /**
     * Tamaño máximo para documentos complementarios (5MB).
     */
    public const DOCUMENT_FILE_SIZE_KB = 5120;
    public const DOCUMENT_FILE_SIZE_BYTES = self::DOCUMENT_FILE_SIZE_KB * 1024;
    public const DOCUMENT_FILE_SIZE_MB = self::DOCUMENT_FILE_SIZE_KB / 1024;

    /**
     * Tamaño máximo para imágenes de perfil (2MB).
     */
    public const IMAGE_FILE_SIZE_KB = 2048;
    public const IMAGE_FILE_SIZE_BYTES = self::IMAGE_FILE_SIZE_KB * 1024;
    public const IMAGE_FILE_SIZE_MB = self::IMAGE_FILE_SIZE_KB / 1024;

    /**
     * MIME types permitidos para archivos Excel/CSV.
     */
    public const EXCEL_MIME_TYPES = [
        'xlsx' => [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/zip',
        ],
        'xls' => [
            'application/vnd.ms-excel',
            'application/msexcel',
        ],
    ];

    /**
     * MIME types permitidos para documentos PDF.
     */
    public const PDF_MIME_TYPES = [
        'application/pdf',
    ];

    /**
     * MIME types permitidos para imágenes.
     */
    public const IMAGE_MIME_TYPES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Convierte bytes a formato legible (KB, MB, GB).
     */
    public static function formatBytes(int $bytes, int $decimals = 2): string
    {
        if ($bytes === 0) {
            return '0 Bytes';
        }

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $i = (int) floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), $decimals) . ' ' . $sizes[$i];
    }

    /**
     * Verifica si el tamaño del archivo está dentro del límite.
     */
    public static function isWithinLimit(int $sizeInBytes, int $limitInBytes): bool
    {
        return $sizeInBytes > 0 && $sizeInBytes <= $limitInBytes;
    }

    /**
     * Obtiene el límite de importación en el formato especificado.
     */
    public static function getImportLimit(string $format = 'MB'): int|float
    {
        return match (strtoupper($format)) {
            'KB' => self::IMPORT_FILE_SIZE_KB,
            'BYTES' => self::IMPORT_FILE_SIZE_BYTES,
            'MB' => self::IMPORT_FILE_SIZE_MB,
            default => self::IMPORT_FILE_SIZE_BYTES,
        };
    }

    /**
     * Obtiene el límite de documentos en el formato especificado.
     */
    public static function getDocumentLimit(string $format = 'MB'): int|float
    {
        return match (strtoupper($format)) {
            'KB' => self::DOCUMENT_FILE_SIZE_KB,
            'BYTES' => self::DOCUMENT_FILE_SIZE_BYTES,
            'MB' => self::DOCUMENT_FILE_SIZE_MB,
            default => self::DOCUMENT_FILE_SIZE_BYTES,
        };
    }

    /**
     * Obtiene el límite de imágenes en el formato especificado.
     */
    public static function getImageLimit(string $format = 'MB'): int|float
    {
        return match (strtoupper($format)) {
            'KB' => self::IMAGE_FILE_SIZE_KB,
            'BYTES' => self::IMAGE_FILE_SIZE_BYTES,
            'MB' => self::IMAGE_FILE_SIZE_MB,
            default => self::IMAGE_FILE_SIZE_BYTES,
        };
    }

    /**
     * Valida que el MIME type coincida con la extensión para archivos Excel/CSV.
     */
    public static function isValidExcelMimeType(string $extension, string $mimeType): bool
    {
        $extension = strtolower($extension);

        if (!isset(self::EXCEL_MIME_TYPES[$extension])) {
            return false;
        }

        return in_array($mimeType, self::EXCEL_MIME_TYPES[$extension], true);
    }

    /**
     * Obtiene información sobre los límites de configuración de PHP recomendados.
     */
    public static function getRecommendedPhpConfig(): array
    {
        return [
            'upload_max_filesize' => '8M',
            'post_max_size' => '8M',
            'memory_limit' => '128M',
            'max_execution_time' => '300',
            'max_input_time' => '300',
        ];
    }

    /**
     * Verifica si la configuración de PHP actual es segura.
     */
    public static function isPhpConfigSafe(): array
    {
        $recommended = self::getRecommendedPhpConfig();
        $current = [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size'       => ini_get('post_max_size'),
            'memory_limit'        => ini_get('memory_limit'),
            'max_execution_time'  => ini_get('max_execution_time'),
            'max_input_time'      => ini_get('max_input_time'),
        ];

        $issues = [];

        // Convertir valores actuales a bytes
        $uploadMaxBytes    = self::convertToBytes($current['upload_max_filesize']);
        $postMaxBytes      = self::convertToBytes($current['post_max_size']);
        $memoryLimitBytes  = self::convertToBytes($current['memory_limit']);

        // Requisito real para upload_max_filesize basado en la recomendación (8M)
        $requiredUploadMax = self::convertToBytes($recommended['upload_max_filesize']);

        if ($uploadMaxBytes < $requiredUploadMax) {
            $issues[] = sprintf(
                'upload_max_filesize (%s) es menor que el límite requerido (%s)',
                $current['upload_max_filesize'],
                $recommended['upload_max_filesize']
            );
        }

        // Requisito real para post_max_size basado en contenido esperado
        $requiredPostMax = max(self::IMPORT_CONTENT_LENGTH_BYTES, self::GENERAL_CONTENT_LENGTH_BYTES);

        if ($postMaxBytes < $requiredPostMax) {
            $issues[] = sprintf(
                'post_max_size (%s) es menor que el límite requerido (%s)',
                $current['post_max_size'],
                self::formatBytes($requiredPostMax, 0)
            );
        }

        if ($memoryLimitBytes < self::convertToBytes($recommended['memory_limit'])) {
            $issues[] = sprintf(
                'memory_limit (%s) es menor que el límite requerido (%s)',
                $current['memory_limit'],
                $recommended['memory_limit']
            );
        }

        return [
            'is_safe'      => empty($issues),
            'current'      => $current,
            'recommended'  => $recommended,
            'issues'       => $issues,
        ];
    }


    /**
     * Convierte valores de configuración de PHP a bytes.
     */
    private static function convertToBytes(string $value): int
    {
        $value = trim($value);
        $unit = strtolower($value[strlen($value) - 1]);
        $number = (int) $value;

        return match ($unit) {
            'g' => $number * 1024 * 1024 * 1024,
            'm' => $number * 1024 * 1024,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
