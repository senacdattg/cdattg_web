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
        Schema::create('instructor_jornada_formacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('instructor_id')->constrained('instructors')->onDelete('cascade');
            $table->foreignId('jornada_formacion_id')->constrained('jornadas_formacion')->onDelete('cascade');
            $table->foreignId('user_create_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('user_edit_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['instructor_id', 'jornada_formacion_id'], 'idx_instructor_jornada_unique');
            
            // Índices para optimizar consultas
            $table->index('instructor_id', 'idx_instructor_jornada_instructor');
            $table->index('jornada_formacion_id', 'idx_instructor_jornada_jornada');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instructor_jornada_formacion');
    }
};

