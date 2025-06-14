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

            $table->foreignId('red_conocimiento_id')->constrained('red_conocimientos');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programas_formacion', function (Blueprint $table) {
            $table->dropForeign(['red_conocimiento_id']);
            $table->dropColumn('red_conocimiento_id');

            $table->foreignId('tipo_programa_id')->constrained('tipos_programas');
        });
    }
};
