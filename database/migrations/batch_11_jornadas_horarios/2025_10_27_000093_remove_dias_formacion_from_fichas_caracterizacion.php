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
        // Eliminar la columna dias_de_formacion de la tabla fichas_caracterizacion
        Schema::table('fichas_caracterizacion', function (Blueprint $table) {
            $table->dropColumn('dias_de_formacion');
        });

        // Eliminar la columna duracion de la tabla programas_formacion
        Schema::table('programas_formacion', function (Blueprint $table) {
            $table->dropColumn('duracion');
            $table->dropForeign(['sede_id']);
            $table->dropColumn('sede_id');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fichas_caracterizacion', function (Blueprint $table) {
            $table->json('dias_de_formacion')->nullable();
        });

        Schema::table('programas_formacion', function (Blueprint $table) {
            $table->integer('duracion')->nullable();
            $table->foreignId('sede_id')->nullable()->constrained('sedes');
        });
    }
};
