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
            // Eliminar columna jornadas JSON ya que las jornadas se almacenan en la tabla pivot instructor_jornada_formacion
            if (Schema::hasColumn('instructors', 'jornadas')) {
                $table->dropColumn('jornadas');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // Restaurar columna jornadas (solo para rollback, no se debe usar)
            $table->json('jornadas')->nullable()->after('centro_formacion_id')->comment('Jornadas (deprecated - usar tabla pivot)');
        });
    }
};
