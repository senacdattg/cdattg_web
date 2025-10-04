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
        Schema::create('complementarios_ofertados', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->integer('duracion');
            $table->integer('cupos');
            $table->tinyInteger('estado')->default(0);
            $table->foreignId('modalidad_id')->constrained('parametros_temas');
            $table->foreignId('jornada_id')->constrained('jornadas_formacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complementarios_ofertados');
    }
};
