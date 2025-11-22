<?php

namespace App\Services;

use App\Models\Persona;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class AspiranteDocumentoService
{
    /**
     * Construir patrón de búsqueda para documento
     */
    public function construirPatronBusqueda(Persona $persona): string
    {
        $tipoDocumento = $persona->tipoDocumento ? str_replace(
            ' ',
            '_',
            $persona->tipoDocumento->name
        ) : 'DOC';

        return "{$tipoDocumento}_{$persona->numero_documento}_" .
            str_replace(' ', '_', $persona->primer_nombre) . "_" .
            str_replace(' ', '_', $persona->primer_apellido) . "_";
    }

    /**
     * Buscar documento en Google Drive
     */
    public function buscarDocumentoEnGoogleDrive(array $files, string $patron): bool
    {
        foreach ($files as $file) {
            $fileName = basename($file);
            if (strpos($fileName, $patron) === 0) {
                try {
                    if (Storage::disk('google')->exists($file)) {
                        return true;
                    }
                } catch (\Exception $e) {
                    Log::warning("Error verificando existencia de archivo: {$fileName}", [
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
        return false;
    }

    /**
     * Obtener archivos de Google Drive
     */
    public function getGoogleDriveFiles(): array
    {
        try {
            $files = Storage::disk('google')->files('documentos_aspirantes');
            Log::info("Total de archivos en Google Drive: " . count($files));
            return $files;
        } catch (\Exception $e) {
            Log::error("Error al listar archivos en Google Drive: " . $e->getMessage());
            throw new \RuntimeException('Error al acceder a Google Drive: ' . $e->getMessage());
        }
    }

    /**
     * Encontrar archivo en Google Drive
     */
    public function encontrarArchivoEnGoogleDrive(string $patron): ?string
    {
        $files = Storage::disk('google')->files('documentos_aspirantes');

        foreach ($files as $file) {
            $fileName = basename($file);
            if (strpos($fileName, $patron) === 0) {
                return $file;
            }
        }

        return null;
    }

    /**
     * Agregar páginas al PDF
     */
    public function agregarPaginasAPDF(Fpdi $pdf, string $tempFilePath): void
    {
        $pageCount = $pdf->setSourceFile($tempFilePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
            $pdf->useTemplate($templateId);
        }
    }

    /**
     * Limpiar archivos temporales
     */
    public function limpiarArchivosTemporales(array $archivosTemporales): void
    {
        foreach ($archivosTemporales as $tempFile) {
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
        }
    }

    /**
     * Crear directorio temporal
     */
    public function createTempDirectory(): string
    {
        $tempDir = storage_path('app/temp');
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        return $tempDir;
    }
}
