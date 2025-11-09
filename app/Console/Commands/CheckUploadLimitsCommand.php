<?php

namespace App\Console\Commands;

use App\Configuration\UploadLimits;
use Illuminate\Console\Command;

/**
 * Comando para verificar que los límites de carga de archivos de PHP sean seguros.
 */
class CheckUploadLimitsCommand extends Command
{
    /**
     * El nombre y firma del comando de consola.
     */
    protected $signature = 'upload:check-limits
                            {--json : Mostrar la salida en formato JSON}';

    /**
     * La descripción del comando de consola.
     */
    protected $description = 'Verifica que la configuración de PHP permita cargas de archivos seguras';

    /**
     * Ejecuta el comando de consola.
     */
    public function handle(): int
    {
        $this->info('🔍 Verificando configuración de límites de carga...');
        $this->newLine();

        $config = UploadLimits::isPhpConfigSafe();

        if ($this->option('json')) {
            $this->line(json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return $config['is_safe'] ? self::SUCCESS : self::FAILURE;
        }

        // Mostrar configuración actual vs recomendada
        $this->info('📋 Configuración Actual vs Recomendada:');
        $this->newLine();

        $headers = ['Parámetro', 'Actual', 'Recomendado', 'Estado'];
        $rows = [];

        foreach ($config['recommended'] as $param => $recommendedValue) {
            $currentValue = $config['current'][$param];
            $status = $this->getStatusForParam($param, $currentValue, $recommendedValue, $config['issues']);

            $rows[] = [
                $param,
                $currentValue,
                $recommendedValue,
                $status,
            ];
        }

        $this->table($headers, $rows);
        $this->newLine();

        // Mostrar límites de la aplicación
        $this->info('📦 Límites de la Aplicación:');
        $this->newLine();

        $limitsTable = [
            ['Tipo', 'Límite'],
            ['Importación de archivos (Excel/CSV)', UploadLimits::formatBytes(UploadLimits::IMPORT_FILE_SIZE_BYTES)],
            ['Content-Length para importaciones', UploadLimits::formatBytes(UploadLimits::IMPORT_CONTENT_LENGTH_BYTES)],
            ['Documentos complementarios', UploadLimits::formatBytes(UploadLimits::DOCUMENT_FILE_SIZE_BYTES)],
            ['Imágenes de perfil', UploadLimits::formatBytes(UploadLimits::IMAGE_FILE_SIZE_BYTES)],
        ];

        $this->table($limitsTable[0], array_slice($limitsTable, 1));
        $this->newLine();

        // Mostrar problemas si existen
        if (!empty($config['issues'])) {
            $this->error('❌ Se encontraron problemas de configuración:');
            $this->newLine();

            foreach ($config['issues'] as $issue) {
                $this->warn("  • {$issue}");
            }

            $this->newLine();
            $this->warn('⚠️  La configuración de PHP actual podría causar problemas al cargar archivos.');
            $this->warn('   Por favor, actualiza los valores en php.ini según lo recomendado.');
            $this->newLine();

            return self::FAILURE;
        }

        $this->info('✅ La configuración de PHP es segura para las cargas de archivos.');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Obtiene el estado visual de un parámetro.
     */
    private function getStatusForParam(string $param, string $current, string $recommended, array $issues): string
    {
        foreach ($issues as $issue) {
            if (str_contains($issue, $param)) {
                return '❌ Insuficiente';
            }
        }

        return '✅ OK';
    }
}

