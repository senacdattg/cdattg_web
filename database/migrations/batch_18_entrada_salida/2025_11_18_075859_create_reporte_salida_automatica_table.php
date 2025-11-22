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
        Schema::create('reporte_salida_automatica', function (Blueprint $table) {
            $table->id();
            $table->date('fecha_procesamiento');
            $table->time('hora_procesamiento');
            $table->integer('total_salidas_procesadas')->default(0);
            $table->json('detalle')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index('fecha_procesamiento');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reporte_salida_automatica');
    }
};
