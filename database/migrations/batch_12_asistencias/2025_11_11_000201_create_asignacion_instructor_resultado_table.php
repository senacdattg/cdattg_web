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
            $table->unsignedBigInteger('resultado_aprendizaje_id');
            $table->timestamps();

            $table->unique(['asignacion_id', 'resultado_aprendizaje_id'], 'asignacion_resultado_unica');
        });

        if (Schema::hasTable('resultados_aprendizajes')) {
            Schema::table('asignacion_instructor_resultado', function (Blueprint $table) {
                $table->foreign('resultado_aprendizaje_id', 'asignacion_resultado_rap_id_foreign')
                    ->references('id')
                    ->on('resultados_aprendizajes')
                    ->cascadeOnDelete();
            });
        } else {
            Schema::table('asignacion_instructor_resultado', function (Blueprint $table) {
                $table->index('resultado_aprendizaje_id', 'asignacion_resultado_rap_id_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_instructor_resultado');
    }
};

