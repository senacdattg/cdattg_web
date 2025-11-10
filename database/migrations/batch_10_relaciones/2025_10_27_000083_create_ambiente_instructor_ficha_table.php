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
        if (Schema::hasTable('ambiente_instructor_ficha')) {
            return;
        }

        Schema::create('ambiente_instructor_ficha', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_asignacion')->nullable();
            $table->foreignId('ambiente_id')->constrained('ambientes');
            $table->foreignId('instructor_ficha_id')->constrained('instructor_fichas_caracterizacion');
            $table->foreignId('vigilante_id')->nullable()->constrained('vigilantes');
            $table->foreignId('instructor_id')->constrained('instructors');
            $table->time('hora_entrada')->nullable();
            $table->time('hora_salida')->nullable();
            $table->foreignId('jornada_id')->constrained('jornadas_formacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambiente_instructor_ficha');
    }
};
