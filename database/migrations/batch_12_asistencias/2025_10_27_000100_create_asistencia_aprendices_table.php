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
        Schema::create('asistencia_aprendices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caracterizacion_id')->constrained('caracterizacion_programas');
            $table->string('nombres');
            $table->string('apellidos');
            $table->time('hora_ingreso');
            $table->time('hora_salida')->nullable();
            $table->bigInteger('numero_identificacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencia_aprendices');
    }
};
