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
        Schema::create('detalle_ordenes', function (Blueprint $table) {
            $table->id();
            
            // Relación con orden
            $table->unsignedBigInteger('orden_id');
            
            // Relación con producto
            $table->unsignedBigInteger('productos_id'); 
            
            // Estado del detalle de la orden
            $table->unsignedBigInteger('estado_orden_id');
            
            // Cantidad solicitada/prestada
            $table->integer('cantidad');
            
            // Auditoría
            $table->unsignedBigInteger('user_create_id');
            $table->unsignedBigInteger('user_update_id');
            $table->timestamps();
            
            // Llaves foráneas
            $table->foreign('orden_id')->references('id')->on('ordenes')->onDelete('cascade');
            $table->foreign('productos_id')->references('id')->on('productos')->onDelete('restrict');
            $table->foreign('estado_orden_id')->references('id')->on('parametros_temas')->onDelete('restrict');
            $table->foreign('user_create_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('user_update_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_ordenes');
    }
};
