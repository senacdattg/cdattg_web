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
            $table->foreignId('evidencia_id')->nullable()->after('aprendiz_ficha_id')->constrained('evidencias');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_aprendices', function (Blueprint $table) {
            $table->dropForeign(['evidencia_id']);
            $table->dropColumn('evidencia_id');
        });
    }
};
