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
        Schema::create('caracterizacion_programas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ficha_id')->constrained('fichas_caracterizacion');
            $table->foreignId('programa_formacion_id')->constrained('programas_formacion');
            $table->foreignId('instructor_id')->constrained('instructors');
            $table->foreignId('jornada_id')->constrained('jornadas_formacion'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas_caracterizacion');
    }
};
