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
        // Tabla pivot para competencias en complementarios
        Schema::create('competencia_complementario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competencia_id')->nullable()->constrained('competencias');
            $table->foreignId('complementario_id')->nullable()->constrained('complementarios_ofertados');
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');
            $table->timestamps();

            // Restricción única para evitar duplicados
            $table->unique(['competencia_id', 'complementario_id'], 'competencia_complementario_unique');
        });

        // Tabla pivot para resultados de aprendizaje en complementarios
        Schema::create('resultado_aprendizaje_complementario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rap_id')->nullable(false)->constrained('resultados_aprendizajes');
            $table->foreignId('complementario_id')->nullable(false)->constrained('complementarios_ofertados');
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');
            $table->timestamps();

            // Restricción única para evitar duplicados
            $table->unique(['rap_id', 'complementario_id'], 'rap_complementario_unique');
        });

        // Tabla pivot para relación entre RAPs y competencias en complementarios
        Schema::create('rap_competencia_complementario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rap_id')->nullable(false)->constrained('resultados_aprendizajes');
            $table->foreignId('competencia_id')->nullable(false)->constrained('competencias');
            $table->foreignId('complementario_id')->nullable(false)->constrained('complementarios_ofertados');
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');
            $table->timestamps();

            // Restricción única para evitar duplicados
            $table->unique(['rap_id', 'competencia_id', 'complementario_id'], 'rap_competencia_complementario_unique');
        });

        // Tabla pivot para guías de aprendizaje en complementarios
        Schema::create('guia_aprendizaje_complementario', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guia_aprendizaje_id')->nullable(false)->constrained('guia_aprendizajes');
            $table->foreignId('complementario_id')->nullable(false)->constrained('complementarios_ofertados');
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');
            $table->timestamps();

            // Restricción única para evitar duplicados
            $table->unique(['guia_aprendizaje_id', 'complementario_id'], 'guia_complementario_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guia_aprendizaje_complementario');
        Schema::dropIfExists('rap_competencia_complementario');
        Schema::dropIfExists('resultado_aprendizaje_complementario');
        Schema::dropIfExists('competencia_complementario');
    }
};
