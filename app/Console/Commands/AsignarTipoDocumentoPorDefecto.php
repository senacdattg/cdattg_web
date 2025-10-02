<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Persona;
use App\Models\Parametro;
use Illuminate\Support\Facades\DB;

class AsignarTipoDocumentoPorDefecto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aprendices:asignar-tipo-documento {--dry-run : Solo mostrar qué se actualizaría sin hacer cambios}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asigna tipo de documento por defecto a personas que no lo tienen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('🔍 Buscando personas sin tipo de documento...');
        
        // Obtener personas sin tipo de documento
        $personasSinTipoDoc = Persona::whereNull('tipo_documento')->get();
        
        if ($personasSinTipoDoc->isEmpty()) {
            $this->info('✅ Todas las personas ya tienen tipo de documento asignado.');
            return 0;
        }
        
        $this->warn("⚠️  Se encontraron {$personasSinTipoDoc->count()} personas sin tipo de documento:");
        
        // Obtener el tipo de documento por defecto (CÉDULA DE CIUDADANÍA)
        $tipoDocDefecto = Parametro::where('name', 'CEDULA DE CIUDADANIA')->first();
        
        if (!$tipoDocDefecto) {
            $this->error('❌ No se encontró el parámetro "CEDULA DE CIUDADANIA"');
            return 1;
        }
        
        $this->info("📋 Tipo de documento por defecto: {$tipoDocDefecto->name} (ID: {$tipoDocDefecto->id})");
        
        // Mostrar algunas personas que se actualizarían
        $this->info('👥 Primeras 5 personas que se actualizarían:');
        $personasSinTipoDoc->take(5)->each(function($persona, $index) {
            $this->line("   " . ($index + 1) . ". {$persona->nombre_completo} - {$persona->numero_documento}");
        });
        
        if ($personasSinTipoDoc->count() > 5) {
            $this->line("   ... y " . ($personasSinTipoDoc->count() - 5) . " más");
        }
        
        if ($isDryRun) {
            $this->info('🔍 Modo dry-run: No se realizarán cambios.');
            $this->info('💡 Ejecuta sin --dry-run para asignar el tipo de documento.');
            return 0;
        }
        
        if (!$this->confirm('¿Deseas asignar el tipo de documento por defecto a todas estas personas?')) {
            $this->info('❌ Operación cancelada.');
            return 0;
        }
        
        $this->info('🔄 Asignando tipo de documento por defecto...');
        
        try {
            DB::beginTransaction();
            
            $actualizadas = Persona::whereNull('tipo_documento')
                ->update(['tipo_documento' => $tipoDocDefecto->id]);
            
            DB::commit();
            
            $this->info("✅ Se actualizaron {$actualizadas} personas con el tipo de documento por defecto.");
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Error durante la actualización: {$e->getMessage()}");
            return 1;
        }
        
        // Verificar que no queden personas sin tipo de documento
        $personasRestantes = Persona::whereNull('tipo_documento')->count();
        
        if ($personasRestantes === 0) {
            $this->info('✅ Verificación: Todas las personas ahora tienen tipo de documento asignado.');
        } else {
            $this->warn("⚠️  Aún quedan {$personasRestantes} personas sin tipo de documento.");
        }
        
        return 0;
    }
}

