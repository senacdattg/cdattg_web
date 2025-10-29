<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AsignarFichasAprendicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener todas las fichas disponibles
        $fichas = \App\Models\FichaCaracterizacion::where('status', 1)->get();
        
        if ($fichas->isEmpty()) {
            $this->command->warn('No hay fichas disponibles');
            return;
        }

        // Obtener aprendices sin ficha asignada
        $aprendices = \App\Models\Aprendiz::whereNull('ficha_caracterizacion_id')->get();
        
        if ($aprendices->isEmpty()) {
            $this->command->info('Todos los aprendices ya tienen ficha asignada');
            return;
        }

        $this->command->info("Asignando fichas a {$aprendices->count()} aprendices...");
        
        $contador = 0;
        foreach ($aprendices as $aprendiz) {
            // Asignar ficha de forma rotativa
            $ficha = $fichas[$contador % $fichas->count()];
            
            $aprendiz->ficha_caracterizacion_id = $ficha->id;
            $aprendiz->save();
            
            // También agregar a la tabla pivot si no existe
            if (!$aprendiz->fichasCaracterizacion()->where('ficha_id', $ficha->id)->exists()) {
                $aprendiz->fichasCaracterizacion()->attach($ficha->id);
            }
            
            $contador++;
        }

        $this->command->info("✅ {$contador} aprendices asignados exitosamente a fichas");
        $this->command->info("Distribución por ficha:");
        
        foreach ($fichas as $ficha) {
            $count = \App\Models\Aprendiz::where('ficha_caracterizacion_id', $ficha->id)->count();
            $this->command->info("  - Ficha {$ficha->ficha}: {$count} aprendices");
        }
    }
}
