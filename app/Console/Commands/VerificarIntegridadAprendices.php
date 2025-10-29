<?php

namespace App\Console\Commands;

use App\Models\Aprendiz;
use Illuminate\Console\Command;

class VerificarIntegridadAprendices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aprendices:verificar-integridad';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica la integridad de los datos de aprendices y muestra registros con problemas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando integridad de datos de aprendices...');
        $this->newLine();

        // Verificar aprendices sin persona asociada
        $aprendicesSinPersona = Aprendiz::whereDoesntHave('persona')
            ->orWhereHas('persona', function ($query) {
                $query->whereNull('id');
            })
            ->get();

        if ($aprendicesSinPersona->count() > 0) {
            $this->error("⚠️  Encontrados {$aprendicesSinPersona->count()} aprendices sin persona asociada:");
            $this->newLine();

            $tableData = [];
            foreach ($aprendicesSinPersona as $aprendiz) {
                $tableData[] = [
                    'ID' => $aprendiz->id,
                    'Persona ID' => $aprendiz->persona_id ?? 'NULL',
                    'Ficha ID' => $aprendiz->ficha_caracterizacion_id ?? 'Sin asignar',
                    'Estado' => $aprendiz->estado ? 'Activo' : 'Inactivo',
                    'Creado' => $aprendiz->created_at->format('Y-m-d H:i:s'),
                ];
            }

            $this->table(
                ['ID', 'Persona ID', 'Ficha ID', 'Estado', 'Creado'],
                $tableData
            );

            $this->newLine();
            $this->warn('💡 Recomendación: Estos registros deben ser corregidos o eliminados.');
            $this->warn('   Puedes editarlos desde el panel de administración o eliminarlos con:');
            $this->line('   php artisan tinker');
            $this->line('   Aprendiz::find(ID)->delete()');
        } else {
            $this->info('✅ Todos los aprendices tienen una persona asociada correctamente.');
        }

        $this->newLine();
        $this->info('✅ Verificación completada.');

        return Command::SUCCESS;
    }
}
