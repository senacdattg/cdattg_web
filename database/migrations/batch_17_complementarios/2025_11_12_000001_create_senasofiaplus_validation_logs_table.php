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
        Schema::create('senasofiaplus_validation_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('aspirante_id');
            $table->enum('accion', ['validar']);
            $table->json('detalles')->nullable();
            $table->enum('resultado', ['exitoso', 'error', 'advertencia']);
            $table->text('mensaje');
            $table->unsignedBigInteger('user_id')->default(1); // Bot user
            $table->timestamp('fecha_accion');
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['aspirante_id', 'fecha_accion']);
            $table->index(['user_id', 'fecha_accion']);
            $table->index(['accion', 'resultado']);
            $table->index('fecha_accion');

            // Claves foráneas
            $table->foreign('aspirante_id')->references('id')->on('aspirantes_complementarios')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('senasofiaplus_validation_logs');
    }
};