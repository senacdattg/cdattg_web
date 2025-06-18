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
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 50)->unique()->nullable(false);
            $table->string('nombre', 255)->nullable(false);
            $table->foreignId('user_create_id')->nullable()->constrained('users');
            $table->foreignId('user_edit_id')->nullable()->constrained('users');
            $table->timestamps();
        });

        $this->evidencia_guia_aprendizaje();
    }

    public function evidencia_guia_aprendizaje(): void
    {
        Schema::create('evidencia_guia_aprendizaje', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evidencia_id')->nullable(false)->constrained('evidencias');
            $table->foreignId('guia_aprendizaje_id')->nullable(false)->constrained('guia_aprendizajes');
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
        Schema::dropIfExists('evidencias');
        Schema::dropIfExists('evidencia_guia_aprendizaje');
    }
};
