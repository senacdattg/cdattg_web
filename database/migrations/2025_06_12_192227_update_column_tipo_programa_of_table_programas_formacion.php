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
        Schema::table('programas_formacion', function (Blueprint $table) {
            $table->dropForeign(['tipo_programa_id']);
            $table->dropColumn('tipo_programa_id');

            $table->foreignId('red_conocimiento_id')->after('nombre')->constrained('red_conocimientos');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero elimina la clave foránea de la tabla caracterizacion_programas
        Schema::table('caracterizacion_programas', function (Blueprint $table) {
            $table->dropForeign(['jornada_id']);
        });

        // Luego elimina la tabla jornadas_formacion
        Schema::dropIfExists('jornadas_formacion');

        Schema::table('programas_formacion', function (Blueprint $table) {
            // Si la columna existe, elimínala primero (esto previene el error)
            if (Schema::hasColumn('programas_formacion', 'red_conocimiento_id')) {
                $table->dropColumn('red_conocimiento_id');
            }
            // Si la columna tipo_programa_id ya existe, no la agregues de nuevo
            if (!Schema::hasColumn('programas_formacion', 'tipo_programa_id')) {
                $table->foreignId('tipo_programa_id')->constrained('tipos_programas');
            }
        });
    }
};
