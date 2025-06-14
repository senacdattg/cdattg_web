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
        Schema::table('asistencia_aprendices', function (Blueprint $table) {
            $table->dropForeign(['caracterizacion_id']);
            $table->dropColumn('caracterizacion_id');
            $table->dropColumn('nombres');
            $table->dropColumn('apellidos');
            $table->dropColumn('numero_identificacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_aprendices', function (Blueprint $table) {
            $table->foreignId('caracterizacion_id')->nullable()->constrained('caracterizaciones');
            $table->string('nombres')->nullable();
            $table->string('apellidos')->nullable();
            $table->string('numero_identificacion')->nullable();
        });
    }
};
