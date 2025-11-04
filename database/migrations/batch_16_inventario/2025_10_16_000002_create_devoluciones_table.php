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
        Schema::create('devoluciones', function (Blueprint $table) {
            $table->id();
            
            // Relación con detalle_orden
            $table->unsignedBigInteger('detalle_orden_id');
            
            // Cantidad devuelta (puede ser parcial)
            $table->integer('cantidad_devuelta');
            
            // Fecha real de devolución
            $table->date('fecha_devolucion');
            
            // Estado de la devolución
            $table->unsignedBigInteger('estado_id');
            
            // Observaciones sobre la devolución
            $table->text('observaciones')->nullable();
            
            // Auditoría
            $table->unsignedBigInteger('user_create_id');
            $table->unsignedBigInteger('user_update_id');
            $table->timestamps();
            
            // Llaves foráneas
            $table->foreign('detalle_orden_id')->references('id')->on('detalle_ordenes')->onDelete('cascade');
            $table->foreign('user_create_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('estado_id')->references('id')->on('parametros_temas')->onDelete('restrict');
            $table->foreign('user_update_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('devoluciones');
    }
};
