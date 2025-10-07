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
        Schema::table('guia_aprendizaje_rap', function (Blueprint $table) {
            // Agregar columna es_obligatorio si no existe
            if (!Schema::hasColumn('guia_aprendizaje_rap', 'es_obligatorio')) {
                $table->boolean('es_obligatorio')->default(true)->after('rap_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guia_aprendizaje_rap', function (Blueprint $table) {
            if (Schema::hasColumn('guia_aprendizaje_rap', 'es_obligatorio')) {
                $table->dropColumn('es_obligatorio');
            }
        });
    }
};
