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
        Schema::create('aprobaciones', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('detalle_orden_id');
            $table->unsignedBigInteger('estado_aprobacion_id');
            $table->unsignedBigInteger('user_create_id');
            $table->unsignedBigInteger('user_update_id');

            $table->timestamps();

            $table->foreign('detalle_orden_id')->references('id')->on('detalle_ordenes')->onDelete('restrict');
            $table->foreign('estado_aprobacion_id')->references('id')->on('parametros_temas')->onDelete('restrict');
            $table->foreign('user_create_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('user_update_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aprobaciones');
    }
};
