<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // Drop old jornada_trabajo_id column since we're using pivot table now
            // Verificar si la columna existe antes de intentar eliminarla
            if (Schema::hasColumn('instructors', 'jornada_trabajo_id')) {
                // Intentar eliminar la foreign key solo si existe
                try {
                    $table->dropForeign(['jornada_trabajo_id']);
                } catch (\Exception $e) {
                    // La foreign key puede no existir, continuar
                }
                $table->dropColumn('jornada_trabajo_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // Restore old column
            $table->foreignId('jornada_trabajo_id')->nullable()->after('centro_formacion_id')->constrained('jornadas_formacion')->onDelete('set null')->comment('Jornada de trabajo');
        });
    }
};

