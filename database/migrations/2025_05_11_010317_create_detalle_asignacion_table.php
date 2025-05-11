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
        Schema::create('detalle_asignacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_asignacion_formacion')->constrained('asignacion_de_formacions');
            $table->foreignId('id_dia_formacion')->constrained('parametros');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_asignacion');
    }
};
