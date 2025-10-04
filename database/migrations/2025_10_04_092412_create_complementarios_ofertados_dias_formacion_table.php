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
        Schema::create('complementarios_ofertados_dias_formacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complementario_id')->constrained('complementarios_ofertados')->onDelete('cascade')->name('comp_ofertados_dias_comp_id_fk');
            $table->foreignId('dia_id')->constrained('parametros_temas')->name('comp_ofertados_dias_dia_id_fk');
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
        Schema::dropIfExists('complementarios_ofertados_dias_formacion');
    }
};
