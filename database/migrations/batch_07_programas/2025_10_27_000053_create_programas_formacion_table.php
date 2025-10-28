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
        Schema::create('programas_formacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_programa_id')->constrained('tipos_programas');
            $table->foreignId('sede_id')->constrained('sedes');
            $table->bigInteger('codigo');
            $table->string('nombre');
            $table->string('duracion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas_formacion');
    }
};