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
            $table->foreignId('competencia_id')
                ->constrained('competencias')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ficha_id', 'instructor_id', 'competencia_id'], 'asignacion_unica');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignaciones_instructores');
    }
};

