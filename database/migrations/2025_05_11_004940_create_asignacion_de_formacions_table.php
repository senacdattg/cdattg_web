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
        Schema::create('asignacion_de_formacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_instructor')->constrained('instructors');
            $table->foreignId('id_ficha')->constrained('fichas_caracterizacion');
            $table->foreignId('id_ambiente')->constrained('ambientes');
            $table->foreignId('id_jornada')->constrained('jornadas_formacion');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asignacion_de_formacions');
    }
};
