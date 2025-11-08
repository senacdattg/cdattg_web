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
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('tipo'); // Tipo de notificación
            $table->morphs('notificable'); // Usuario que recibe
            $table->text('datos'); // Datos de la notificación
            $table->timestamp('leida_en')->nullable(); // Fecha de lectura
            $table->timestamps();
            
            $table->index(['notificable_type', 'notificable_id', 'leida_en']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
