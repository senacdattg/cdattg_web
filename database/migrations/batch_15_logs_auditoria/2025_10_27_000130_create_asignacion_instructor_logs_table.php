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
        Schema::create('asignacion_instructor_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('instructor_id');
            $table->unsignedBigInteger('ficha_id');
            $table->enum('accion', ['asignar', 'desasignar', 'actualizar', 'validar']);
            $table->json('detalles')->nullable();
            $table->enum('resultado', ['exitoso', 'error', 'advertencia']);
            $table->text('mensaje');
            $table->unsignedBigInteger('user_id');
            $table->timestamp('fecha_accion');
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->timestamps();

            // Índices para optimizar consultas
            $table->index(['instructor_id', 'fecha_accion']);
            $table->index(['ficha_id', 'fecha_accion']);
            $table->index(['user_id', 'fecha_accion']);
            $table->index(['accion', 'resultado']);
            $table->index('fecha_accion');

            // Claves foráneas
            $table->foreign('instructor_id')->references('id')->on('instructors')->onDelete('cascade');
            $table->foreign('ficha_id')->references('id')->on('fichas_caracterizacion')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_instructor_logs');
    }
};