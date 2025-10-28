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
            $table->foreignId('instructor_ficha_id')->after('id')->nullable()->constrained('instructor_fichas_caracterizacion');
            $table->foreignId('aprendiz_ficha_id')->after('instructor_ficha_id')->nullable()->constrained('aprendiz_fichas_caracterizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_aprendices', function (Blueprint $table) {
            //
        });
    }
};
