<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class RefactorSonarQubeCommand extends Command
{
    /**
     * Nombre y firma del comando de consola
     *
     * @var string
     */
    protected $signature = 'refactor:sonarqube 
                            {--dry-run : Ejecutar sin aplicar cambios}
                            {--path=app : Ruta específica a analizar}';

    /**
     * Descripción del comando de consola
     *
     * @var string
     */
    protected $description = 'Analiza y corrige problemas de mantenibilidad detectados por SonarQube';

    private array $stats = [
        'archivos_analizados' => 0,
        'errores_encontrados' => 0,
        'errores_corregidos' => 0,
        'archivos_modificados' => []
    ];

    /**
     * Ejecutar comando de consola
     */
    public function handle(): int
    {
        // Validar que solo se ejecute en desarrollo
        if (!app()->environment(['local', 'development', 'testing'])) {
            $this->error('❌ Este comando solo puede ejecutarse en entorno de desarrollo');
            return self::FAILURE;
        }

        $dryRun = $this->option('dry-run');
        $targetPath = $this->option('path');

        $this->info('🤖 Agente de Refactorización SonarQube');
        $this->line('📁 Ruta base: ' . base_path());
        $this->line('🎯 Analizando: ' . $targetPath);
        
        if ($dryRun) {
            $this->warn('🔍 Modo DRY-RUN (sin cambios)');
        } else {
            $this->info('✏️  Modo CORRECCIÓN (aplicará cambios)');
        }
        
        $this->newLine();

        $fullPath = base_path($targetPath);
        
        if (!file_exists($fullPath)) {
            $this->error("❌ La ruta {$targetPath} no existe");
            return self::FAILURE;
        }

        $files = $this->findPhpFiles($fullPath);
        
        if (empty($files)) {
            $this->warn('⚠️  No se encontraron archivos PHP en la ruta especificada');
            return self::SUCCESS;
        }

        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->setFormat('verbose');

        foreach ($files as $file) {
            $this->analyzeFile($file, $dryRun);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->printReport($dryRun);

        return self::SUCCESS;
    }

    /**
     * Buscar todos los archivos PHP recursivamente
     */
    private function findPhpFiles(string $directory): array
    {
        $files = [];

        if (!is_dir($directory)) {
            return is_file($directory) && pathinfo($directory, PATHINFO_EXTENSION) === 'php' 
                ? [$directory] 
                : [];
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Analizar un archivo y aplicar correcciones
     */
    private function analyzeFile(string $filePath, bool $dryRun): void
    {
        $this->stats['archivos_analizados']++;
        
        $content = file_get_contents($filePath);
        $originalContent = $content;
        $erroresEnArchivo = 0;

        // Aplicar correcciones
        $content = $this->fixTrailingWhitespace($content, $erroresEnArchivo);
        $content = $this->fixCountToEmpty($content, $erroresEnArchivo);

        if ($content !== $originalContent) {
            $this->stats['errores_encontrados'] += $erroresEnArchivo;
            
            if (!$dryRun) {
                file_put_contents($filePath, $content);
                $this->stats['errores_corregidos'] += $erroresEnArchivo;
                $this->stats['archivos_modificados'][] = $this->getRelativePath($filePath);
            }
        }
    }

    /**
     * Corregir espacios en blanco al final de líneas
     */
    private function fixTrailingWhitespace(string $content, int &$count): string
    {
        $lines = explode("\n", $content);
        $fixed = false;

        foreach ($lines as &$line) {
            if (preg_match('/\s+$/', $line)) {
                $line = rtrim($line);
                $count++;
                $fixed = true;
            }
        }

        return $fixed ? implode("\n", $lines) : $content;
    }

    /**
     * Reemplazar count($array) > 0 con !empty($array)
     */
    private function fixCountToEmpty(string $content, int &$count): string
    {
        $patterns = [
            '/count\((\$\w+)\)\s*>\s*0/' => '!empty($1)',
            '/count\((\$\w+)\)\s*==\s*0/' => 'empty($1)',
            '/count\((\$\w+)\)\s*===\s*0/' => 'empty($1)',
        ];

        foreach ($patterns as $pattern => $replacement) {
            $newContent = preg_replace($pattern, $replacement, $content, -1, $replacements);
            if ($replacements > 0) {
                $content = $newContent;
                $count += $replacements;
            }
        }

        return $content;
    }

    /**
     * Obtener ruta relativa desde base_path
     */
    private function getRelativePath(string $filePath): string
    {
        return str_replace(base_path() . DIRECTORY_SEPARATOR, '', $filePath);
    }

    /**
     * Imprimir reporte final
     */
    private function printReport(bool $dryRun): void
    {
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('📊 REPORTE FINAL');
        $this->info('═══════════════════════════════════════════════════════════');
        $this->newLine();

        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Archivos analizados', $this->stats['archivos_analizados']],
                ['Errores encontrados', $this->stats['errores_encontrados']],
                ['Errores corregidos', $dryRun ? 'N/A (dry-run)' : $this->stats['errores_corregidos']],
                ['Archivos modificados', $dryRun ? 'N/A (dry-run)' : count($this->stats['archivos_modificados'])],
            ]
        );

        if (!$dryRun && !empty($this->stats['archivos_modificados'])) {
            $this->newLine();
            $this->info('📝 Archivos modificados:');
            foreach ($this->stats['archivos_modificados'] as $file) {
                $this->line("  • {$file}");
            }
        }

        $this->newLine();
        
        if ($dryRun) {
            $this->warn('🔍 Modo DRY-RUN: Sin cambios aplicados');
            $this->info('💡 Ejecuta sin --dry-run para aplicar las correcciones');
        } else {
            $this->info('✨ Proceso completado con éxito');
        }
    }
}

