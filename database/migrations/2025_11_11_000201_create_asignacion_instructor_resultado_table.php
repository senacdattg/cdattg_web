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
        Schema::create('asignacion_instructor_resultado', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asignacion_id')
                ->constrained('asignaciones_instructores')
                ->cascadeOnDelete();
            $table->foreignId('resultado_aprendizaje_id')
                ->constrained('resultados_aprendizajes')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['asignacion_id', 'resultado_aprendizaje_id'], 'asignacion_resultado_unica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_instructor_resultado');
    }
};

