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
        Schema::create('ficha_dias_formacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ficha_id')->constrained('fichas_caracterizacion');
            $table->foreignId('dia_id')->constrained('parametros_temas');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ficha_dias_formacion');
    }
};
