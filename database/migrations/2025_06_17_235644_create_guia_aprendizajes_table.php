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
        Schema::create('guia_aprendizajes', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->nullable(false);
            $table->string('nombre', 255)->nullable(false);
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        $this->guia_aprendizaje_competencia();
    }

    public function guia_aprendizaje_competencia(): void
    {
        Schema::create('guia_aprendizaje_competencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guia_aprendizaje_id')->nullable(false)->constrained('guia_aprendizajes');
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
        Schema::dropIfExists('guia_aprendizajes');
        Schema::dropIfExists('guia_aprendizaje_competencia');
    }
};
