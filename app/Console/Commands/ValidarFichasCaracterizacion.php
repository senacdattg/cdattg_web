<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FichaCaracterizacion;
use App\Services\FichaCaracterizacionValidationService;
use Illuminate\Support\Facades\Log;

class ValidarFichasCaracterizacion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fichas:validar {--id= : ID específico de la ficha a validar} {--all : Validar todas las fichas} {--fix : Intentar corregir errores automáticamente}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valida las fichas de caracterización según las reglas de negocio del SENA';

    /**
     * El servicio de validación
     */
    protected $validationService;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->validationService = new FichaCaracterizacionValidationService();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Iniciando validación de fichas de caracterización...');
        
        $fichaId = $this->option('id');
        $validarTodas = $this->option('all');
        $corregirErrores = $this->option('fix');

        try {
            if ($fichaId) {
                $this->validarFichaEspecifica($fichaId, $corregirErrores);
            } elseif ($validarTodas) {
                $this->validarTodasLasFichas($corregirErrores);
            } else {
                $this->error('Debe especificar --id=<ID> para validar una ficha específica o --all para validar todas las fichas.');
                return 1;
            }

            $this->info('✅ Validación completada exitosamente.');
            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Error durante la validación: ' . $e->getMessage());
            Log::error('Error en comando de validación de fichas', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return 1;
        }
    }

    /**
     * Valida una ficha específica.
     */
    private function validarFichaEspecifica($fichaId, $corregirErrores = false)
    {
        $this->info("📋 Validando ficha ID: {$fichaId}");

        $ficha = FichaCaracterizacion::find($fichaId);
        if (!$ficha) {
            $this->error("❌ No se encontró la ficha con ID: {$fichaId}");
            return;
        }

        $this->mostrarInformacionFicha($ficha);
        
        $datos = $ficha->toArray();
        $resultado = $this->validationService->validarFichaCompleta($datos, $ficha->id);

        $this->mostrarResultadoValidacion($resultado);

        if ($corregirErrores && !$resultado['valido']) {
            $this->intentarCorregirErrores($ficha, $resultado['errores']);
        }
    }

    /**
     * Valida todas las fichas activas.
     */
    private function validarTodasLasFichas($corregirErrores = false)
    {
        $this->info('📋 Validando todas las fichas de caracterización...');

        $fichas = FichaCaracterizacion::where('status', true)->get();
        $totalFichas = $fichas->count();
        $fichasValidas = 0;
        $fichasConErrores = 0;
        $fichasConAdvertencias = 0;

        $this->info("Total de fichas a validar: {$totalFichas}");

        $bar = $this->output->createProgressBar($totalFichas);
        $bar->start();

        foreach ($fichas as $ficha) {
            $datos = $ficha->toArray();
            $resultado = $this->validationService->validarFichaCompleta($datos, $ficha->id);

            if ($resultado['valido']) {
                $fichasValidas++;
            } else {
                $fichasConErrores++;
            }

            if (count($resultado['advertencias']) > 0) {
                $fichasConAdvertencias++;
            }

            if ($corregirErrores && !$resultado['valido']) {
                $this->intentarCorregirErrores($ficha, $resultado['errores']);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        // Mostrar resumen
        $this->info('📊 Resumen de la validación:');
        $this->table(
            ['Estado', 'Cantidad', 'Porcentaje'],
            [
                ['✅ Válidas', $fichasValidas, round(($fichasValidas / $totalFichas) * 100, 2) . '%'],
                ['❌ Con Errores', $fichasConErrores, round(($fichasConErrores / $totalFichas) * 100, 2) . '%'],
                ['⚠️ Con Advertencias', $fichasConAdvertencias, round(($fichasConAdvertencias / $totalFichas) * 100, 2) . '%'],
            ]
        );
    }

    /**
     * Muestra la información básica de una ficha.
     */
    private function mostrarInformacionFicha($ficha)
    {
        $this->info("📄 Ficha: {$ficha->ficha}");
        $this->info("📚 Programa: " . ($ficha->programaFormacion->nombre ?? 'N/A'));
        $this->info("📅 Fechas: " . ($ficha->fecha_inicio ?? 'N/A') . " - " . ($ficha->fecha_fin ?? 'N/A'));
        $this->info("👨‍🏫 Instructor: " . ($ficha->instructor ? $ficha->instructor->persona->primer_nombre . ' ' . $ficha->instructor->persona->primer_apellido : 'N/A'));
        $this->info("🏢 Ambiente: " . ($ficha->ambiente->nombre_ambiente ?? 'N/A'));
        $this->newLine();
    }

    /**
     * Muestra el resultado de la validación.
     */
    private function mostrarResultadoValidacion($resultado)
    {
        if ($resultado['valido']) {
            $this->info('✅ La ficha es VÁLIDA');
        } else {
            $this->error('❌ La ficha es INVÁLIDA');
        }

        if (count($resultado['errores']) > 0) {
            $this->error('🚨 Errores encontrados:');
            foreach ($resultado['errores'] as $error) {
                $this->error("   - {$error}");
            }
        }

        if (count($resultado['advertencias']) > 0) {
            $this->warn('⚠️ Advertencias:');
            foreach ($resultado['advertencias'] as $advertencia) {
                $this->warn("   - {$advertencia}");
            }
        }

        $this->newLine();
    }

    /**
     * Intenta corregir errores automáticamente.
     */
    private function intentarCorregirErrores($ficha, $errores)
    {
        $this->warn('🔧 Intentando corregir errores automáticamente...');

        foreach ($errores as $error) {
            $this->warn("   - {$error}");
            
            // Aquí se pueden implementar correcciones automáticas específicas
            // Por ejemplo, ajustar fechas, cambiar ambientes, etc.
        }

        $this->info('💡 Las correcciones automáticas están en desarrollo.');
    }

    /**
     * Genera un reporte de validación.
     */
    private function generarReporte($fichas, $resultados)
    {
        $this->info('📄 Generando reporte de validación...');
        
        // Aquí se puede implementar la generación de un reporte en PDF o Excel
        // con los resultados de la validación
    }
}
