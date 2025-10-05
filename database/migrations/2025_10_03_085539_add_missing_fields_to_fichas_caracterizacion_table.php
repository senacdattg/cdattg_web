<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Exception;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fichas_caracterizacion', function (Blueprint $table) {
            // Solo agregar campos que realmente no existen
            
            // Campo de estado (status) - probablemente no existe
            if (!Schema::hasColumn('fichas_caracterizacion', 'status')) {
                $table->boolean('status')->default(true)->after('total_horas');
            }
            
            // Cambiar el tipo de columna 'ficha' de bigInteger a string si es necesario
            if (Schema::hasColumn('fichas_caracterizacion', 'ficha')) {
                $table->string('ficha', 50)->nullable()->change();
            }
            
            // Hacer instructor_id nullable si no lo es ya
            if (Schema::hasColumn('fichas_caracterizacion', 'instructor_id')) {
                $table->unsignedBigInteger('instructor_id')->nullable()->change();
            }
            
            // Nota: La foreign key de modalidad_formacion_id se manejará en una migración separada si es necesario
            // ya que puede requerir cambios complejos en las foreign keys existentes
        });
        
        // Agregar índices para optimizar consultas frecuentes
        Schema::table('fichas_caracterizacion', function (Blueprint $table) {
            // Índice para búsquedas por número de ficha
            if (!$this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_ficha')) {
                $table->index('ficha', 'idx_fichas_caracterizacion_ficha');
            }
            
            // Índice para búsquedas por programa de formación
            if (!$this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_programa')) {
                $table->index('programa_formacion_id', 'idx_fichas_caracterizacion_programa');
            }
            
            // Índice para búsquedas por instructor
            if (!$this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_instructor')) {
                $table->index('instructor_id', 'idx_fichas_caracterizacion_instructor');
            }
            
            // Índice para búsquedas por sede
            if (!$this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_sede')) {
                $table->index('sede_id', 'idx_fichas_caracterizacion_sede');
            }
            
            // Índice para búsquedas por estado
            if (!$this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_status')) {
                $table->index('status', 'idx_fichas_caracterizacion_status');
            }
            
            // Índice para búsquedas por fecha de inicio
            if (!$this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_fecha_inicio')) {
                $table->index('fecha_inicio', 'idx_fichas_caracterizacion_fecha_inicio');
            }
            
            // Índice compuesto para consultas frecuentes (programa + estado)
            if (!$this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_programa_status')) {
                $table->index(['programa_formacion_id', 'status'], 'idx_fichas_caracterizacion_programa_status');
            }
            
            // Índice compuesto para consultas por instructor y estado
            if (!$this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_instructor_status')) {
                $table->index(['instructor_id', 'status'], 'idx_fichas_caracterizacion_instructor_status');
            }
            
            // Índice único para el número de ficha
            if (!$this->indexExists('fichas_caracterizacion', 'unique_fichas_caracterizacion_ficha')) {
                $table->unique('ficha', 'unique_fichas_caracterizacion_ficha');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fichas_caracterizacion', function (Blueprint $table) {
            // Eliminar índices si existen
            if ($this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_ficha')) {
                $table->dropIndex('idx_fichas_caracterizacion_ficha');
            }
            if ($this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_programa')) {
                $table->dropIndex('idx_fichas_caracterizacion_programa');
            }
            if ($this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_instructor')) {
                $table->dropIndex('idx_fichas_caracterizacion_instructor');
            }
            if ($this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_sede')) {
                $table->dropIndex('idx_fichas_caracterizacion_sede');
            }
            if ($this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_status')) {
                $table->dropIndex('idx_fichas_caracterizacion_status');
            }
            if ($this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_fecha_inicio')) {
                $table->dropIndex('idx_fichas_caracterizacion_fecha_inicio');
            }
            if ($this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_programa_status')) {
                $table->dropIndex('idx_fichas_caracterizacion_programa_status');
            }
            if ($this->indexExists('fichas_caracterizacion', 'idx_fichas_caracterizacion_instructor_status')) {
                $table->dropIndex('idx_fichas_caracterizacion_instructor_status');
            }
            if ($this->indexExists('fichas_caracterizacion', 'unique_fichas_caracterizacion_ficha')) {
                $table->dropUnique('unique_fichas_caracterizacion_ficha');
            }
            
            // Eliminar solo la columna status que agregamos
            if (Schema::hasColumn('fichas_caracterizacion', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
    
    /**
     * Verificar si un índice existe en la tabla
     */
    private function indexExists(string $table, string $index): bool
    {
        try {
            $indexes = DB::select("SHOW INDEX FROM {$table}");
            foreach ($indexes as $idx) {
                if ($idx->Key_name === $index) {
                    return true;
                }
            }
            return false;
        } catch (Exception $e) {
            return false;
        }
    }
};
