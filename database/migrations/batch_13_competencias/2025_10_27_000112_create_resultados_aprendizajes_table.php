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
        Schema::create('resultados_aprendizajes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->nullable(false);
            $table->string('nombre')->nullable(false);
            $table->float('duracion', 8, 2)->nullable(false);
            $table->date('fecha_inicio')->nullable(false);
            $table->date('fecha_fin')->nullable(false);
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');    
            $table->timestamps();
        });

        $this->resultados_aprendizaje_competencia();
    }

    public function resultados_aprendizaje_competencia(): void
    {
        Schema::create('resultados_aprendizaje_competencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rap_id')->nullable(false)->constrained('resultados_aprendizajes');
            $table->foreignId('competencia_id')->nullable(false)->constrained('competencias');
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('resultados_aprendizajes');
        Schema::dropIfExists('resultados_aprendizaje_competencia');
    }
};
