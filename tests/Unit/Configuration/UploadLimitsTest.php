<?php

namespace Tests\Unit\Configuration;

use App\Configuration\UploadLimits;
use Tests\TestCase;

class UploadLimitsTest extends TestCase
{
    public function test_constantes_de_importacion_son_consistentes(): void
    {
        $this->assertEquals(20480, UploadLimits::IMPORT_FILE_SIZE_KB);
        $this->assertEquals(20480 * 1024, UploadLimits::IMPORT_FILE_SIZE_BYTES);
        $this->assertEquals(20, UploadLimits::IMPORT_FILE_SIZE_MB);
        $this->assertEquals(26214400, UploadLimits::IMPORT_CONTENT_LENGTH_BYTES);
    }

    public function test_constantes_de_documentos_son_consistentes(): void
    {
        $this->assertEquals(5120, UploadLimits::DOCUMENT_FILE_SIZE_KB);
        $this->assertEquals(5120 * 1024, UploadLimits::DOCUMENT_FILE_SIZE_BYTES);
        $this->assertEquals(5, UploadLimits::DOCUMENT_FILE_SIZE_MB);
    }

    public function test_constantes_de_imagenes_son_consistentes(): void
    {
        $this->assertEquals(2048, UploadLimits::IMAGE_FILE_SIZE_KB);
        $this->assertEquals(2048 * 1024, UploadLimits::IMAGE_FILE_SIZE_BYTES);
        $this->assertEquals(2, UploadLimits::IMAGE_FILE_SIZE_MB);
    }

    public function test_format_bytes_formatea_correctamente(): void
    {
        $this->assertEquals('0 Bytes', UploadLimits::formatBytes(0));
        $this->assertEquals('1 KB', UploadLimits::formatBytes(1024));
        $this->assertEquals('1 MB', UploadLimits::formatBytes(1024 * 1024));
        $this->assertEquals('1 GB', UploadLimits::formatBytes(1024 * 1024 * 1024));
        $this->assertEquals('1.5 MB', UploadLimits::formatBytes(1536 * 1024));
    }

    public function test_is_within_limit_valida_correctamente(): void
    {
        $limit = 1024 * 1024; // 1MB

        $this->assertTrue(UploadLimits::isWithinLimit(500 * 1024, $limit)); // 500KB
        $this->assertTrue(UploadLimits::isWithinLimit(1024 * 1024, $limit)); // Exactamente 1MB
        $this->assertFalse(UploadLimits::isWithinLimit(2 * 1024 * 1024, $limit)); // 2MB
        $this->assertFalse(UploadLimits::isWithinLimit(0, $limit)); // 0 bytes
        $this->assertFalse(UploadLimits::isWithinLimit(-100, $limit)); // Negativo
    }

    public function test_get_import_limit_retorna_formatos_correctos(): void
    {
        $this->assertEquals(20480, UploadLimits::getImportLimit('KB'));
        $this->assertEquals(20480 * 1024, UploadLimits::getImportLimit('BYTES'));
        $this->assertEquals(20, UploadLimits::getImportLimit('MB'));
        $this->assertEquals(20480 * 1024, UploadLimits::getImportLimit('invalid'));
    }

    public function test_get_document_limit_retorna_formatos_correctos(): void
    {
        $this->assertEquals(5120, UploadLimits::getDocumentLimit('KB'));
        $this->assertEquals(5120 * 1024, UploadLimits::getDocumentLimit('BYTES'));
        $this->assertEquals(5, UploadLimits::getDocumentLimit('MB'));
    }

    public function test_get_image_limit_retorna_formatos_correctos(): void
    {
        $this->assertEquals(2048, UploadLimits::getImageLimit('KB'));
        $this->assertEquals(2048 * 1024, UploadLimits::getImageLimit('BYTES'));
        $this->assertEquals(2, UploadLimits::getImageLimit('MB'));
    }

    public function test_is_valid_excel_mime_type_valida_xlsx(): void
    {
        $this->assertTrue(UploadLimits::isValidExcelMimeType('xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'));
        $this->assertTrue(UploadLimits::isValidExcelMimeType('xlsx', 'application/zip'));
        $this->assertFalse(UploadLimits::isValidExcelMimeType('xlsx', 'text/plain'));
    }

    public function test_is_valid_excel_mime_type_valida_xls(): void
    {
        $this->assertTrue(UploadLimits::isValidExcelMimeType('xls', 'application/vnd.ms-excel'));
        $this->assertTrue(UploadLimits::isValidExcelMimeType('xls', 'application/msexcel'));
        $this->assertFalse(UploadLimits::isValidExcelMimeType('xls', 'text/csv'));
    }

    public function test_is_valid_excel_mime_type_valida_csv(): void
    {
        $this->assertTrue(UploadLimits::isValidExcelMimeType('csv', 'text/csv'));
        $this->assertTrue(UploadLimits::isValidExcelMimeType('csv', 'text/plain'));
        $this->assertTrue(UploadLimits::isValidExcelMimeType('csv', 'application/csv'));
        $this->assertFalse(UploadLimits::isValidExcelMimeType('csv', 'application/pdf'));
    }

    public function test_is_valid_excel_mime_type_rechaza_extension_invalida(): void
    {
        $this->assertFalse(UploadLimits::isValidExcelMimeType('pdf', 'application/pdf'));
        $this->assertFalse(UploadLimits::isValidExcelMimeType('txt', 'text/plain'));
    }

    public function test_get_recommended_php_config_retorna_estructura_correcta(): void
    {
        $config = UploadLimits::getRecommendedPhpConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('upload_max_filesize', $config);
        $this->assertArrayHasKey('post_max_size', $config);
        $this->assertArrayHasKey('memory_limit', $config);
        $this->assertArrayHasKey('max_execution_time', $config);
        $this->assertArrayHasKey('max_input_time', $config);

        $this->assertEquals('20M', $config['upload_max_filesize']);
        $this->assertEquals('25M', $config['post_max_size']);
        $this->assertEquals('128M', $config['memory_limit']);
        $this->assertEquals('300', $config['max_execution_time']);
        $this->assertEquals('300', $config['max_input_time']);
    }

    public function test_is_php_config_safe_retorna_estructura_correcta(): void
    {
        $result = UploadLimits::isPhpConfigSafe();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('is_safe', $result);
        $this->assertArrayHasKey('current', $result);
        $this->assertArrayHasKey('recommended', $result);
        $this->assertArrayHasKey('issues', $result);

        $this->assertIsBool($result['is_safe']);
        $this->assertIsArray($result['current']);
        $this->assertIsArray($result['recommended']);
        $this->assertIsArray($result['issues']);
    }

    public function test_mime_types_constantes_son_arrays_validos(): void
    {
        $this->assertIsArray(UploadLimits::EXCEL_MIME_TYPES);
        $this->assertArrayHasKey('xlsx', UploadLimits::EXCEL_MIME_TYPES);
        $this->assertArrayHasKey('xls', UploadLimits::EXCEL_MIME_TYPES);
        $this->assertArrayHasKey('csv', UploadLimits::EXCEL_MIME_TYPES);

        $this->assertIsArray(UploadLimits::PDF_MIME_TYPES);
        $this->assertContains('application/pdf', UploadLimits::PDF_MIME_TYPES);

        $this->assertIsArray(UploadLimits::IMAGE_MIME_TYPES);
        $this->assertContains('image/jpeg', UploadLimits::IMAGE_MIME_TYPES);
        $this->assertContains('image/png', UploadLimits::IMAGE_MIME_TYPES);
    }

    public function test_content_length_es_mayor_que_file_size(): void
    {
        $this->assertGreaterThan(
            UploadLimits::IMPORT_FILE_SIZE_BYTES,
            UploadLimits::IMPORT_CONTENT_LENGTH_BYTES,
            'El límite de Content-Length debe ser mayor que el límite del archivo para permitir otros datos del formulario'
        );
    }
}

