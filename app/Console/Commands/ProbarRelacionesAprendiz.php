<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Aprendiz;

class ProbarRelacionesAprendiz extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aprendices:probar-relaciones {id : ID del aprendiz a probar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba las relaciones del aprendiz para verificar tipo de documento y jornada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->argument('id');
        
        $this->info("🔍 Probando relaciones del aprendiz ID: {$id}");
        
        try {
            // Cargar el aprendiz con todas las relaciones necesarias
            $aprendiz = Aprendiz::with([
                'persona.tipoDocumento',
                'fichaCaracterizacion.programaFormacion',
                'fichaCaracterizacion.jornadaFormacion',
            ])->findOrFail($id);
            
            $this->info('✅ Aprendiz encontrado');
            $this->line("   Nombre: " . ($aprendiz->persona?->nombre_completo ?? 'N/A'));
            $this->line("   Documento: " . ($aprendiz->persona?->numero_documento ?? 'N/A'));
            
            // Probar tipo de documento
            $this->info('📋 Tipo de Documento:');
            if ($aprendiz->persona?->tipoDocumento) {
                $this->line("   ✅ Relación cargada: " . $aprendiz->persona->tipoDocumento->name);
            } else {
                $this->warn("   ⚠️  No se pudo cargar el tipo de documento");
                $this->line("   ID tipo_documento en persona: " . ($aprendiz->persona?->tipo_documento ?? 'NULL'));
            }
            
            // Probar jornada
            $this->info('🕐 Jornada:');
            if ($aprendiz->fichaCaracterizacion?->jornadaFormacion) {
                $this->line("   ✅ Relación cargada: " . $aprendiz->fichaCaracterizacion->jornadaFormacion->jornada);
            } else {
                $this->warn("   ⚠️  No se pudo cargar la jornada");
                $this->line("   ID jornada_id en ficha: " . ($aprendiz->fichaCaracterizacion?->jornada_id ?? 'NULL'));
            }
            
            // Mostrar información completa
            $this->info('📊 Información completa:');
            $this->line("   Ficha: " . ($aprendiz->fichaCaracterizacion?->ficha ?? 'N/A'));
            $this->line("   Programa: " . ($aprendiz->fichaCaracterizacion?->programaFormacion?->nombre ?? 'N/A'));
            
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return 1;
        }
        
        $this->info('✅ Prueba completada.');
        
        return 0;
    }
}

