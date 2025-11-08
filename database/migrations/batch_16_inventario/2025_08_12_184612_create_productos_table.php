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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();

            $table->string('producto');
            $table->unsignedBigInteger('tipo_producto_id');
            $table->text('descripcion');
            $table->unsignedBigInteger('unidad_medida_id');
            $table->unsignedBigInteger('estado_id');

            $table->unsignedBigInteger('user_create_id');
            $table->unsignedBigInteger('user_update_id');

            $table->timestamps();

            //Se relacionan las llaves foráneas
            $table->foreign('tipo_producto_id')->references('id')->on('parametros_temas')->onDelete('restrict');
            $table->foreign('unidad_medida_id')->references('id')->on('parametros_temas')->onDelete('restrict');
            $table->foreign('estado_id')->references('id')->on('parametros_temas')->onDelete('restrict');
            
            $table->foreign('user_create_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('user_update_id')->references('id')->on('users')->onDelete('restrict');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
