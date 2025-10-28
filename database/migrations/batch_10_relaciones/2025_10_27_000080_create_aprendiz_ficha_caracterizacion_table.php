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
        Schema::create('aprendiz_fichas_caracterizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aprendiz_id')->constrained('aprendices');
            $table->foreignId('ficha_id')->constrained('fichas_caracterizacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('asistencia_aprendices', function (Blueprint $table) {
            $table->dropForeign(['aprendiz_ficha_id']);
        });
        Schema::dropIfExists('aprendiz_fichas_caracterizacion');
    }
};
