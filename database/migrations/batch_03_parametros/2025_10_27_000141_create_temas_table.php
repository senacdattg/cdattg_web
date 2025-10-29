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
        // Crear la tabla de temas
        Schema::create('temas', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('status')->default(1);
            $table->foreignId('user_create_id')->constrained('users')->default(1);
            $table->foreignId('user_edit_id')->constrained('users')->default(1);
            $table->timestamps();
        });

        // Crear la tabla pivote para relacionar temas y parámetros
        Schema::create('parametros_temas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tema_id')->constrained('temas')->onDelete('cascade');
            $table->foreignId('parametro_id')->constrained('parametros')->onDelete('cascade');
            $table->foreignId('user_create_id')->constrained('users')->default(1);
            $table->foreignId('user_edit_id')->constrained('users')->default(1);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros_temas');
        Schema::dropIfExists('temas');
    }
};
