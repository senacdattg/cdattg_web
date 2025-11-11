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
        Schema::create('asignaciones_instructores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ficha_id')
                ->constrained('fichas_caracterizacion')
                ->cascadeOnDelete();
            $table->foreignId('instructor_id')
                ->constrained('instructors')
                ->cascadeOnDelete();
            $table->unsignedBigInteger('competencia_id');
            $table->timestamps();

            $table->unique(['ficha_id', 'instructor_id', 'competencia_id'], 'asignacion_unica');
        });

        if (Schema::hasTable('competencias')) {
            Schema::table('asignaciones_instructores', function (Blueprint $table) {
                $table->foreign('competencia_id', 'asignaciones_instructores_competencia_id_foreign')
                    ->references('id')
                    ->on('competencias')
                    ->cascadeOnDelete();
            });
        } else {
            Schema::table('asignaciones_instructores', function (Blueprint $table) {
                $table->index('competencia_id', 'asignaciones_instructores_competencia_id_index');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones_instructores');
    }
};

