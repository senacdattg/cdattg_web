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
        Schema::create('ordenes', function (Blueprint $table) {
            $table->id();

            $table->text('descripcion_orden');
            $table->unsignedBigInteger('tipo_orden_id');

            $table->unsignedBigInteger('user_create_id');
            $table->unsignedBigInteger('user_update_id');
            
            $table->timestamps();

            //Se relacionan las llaves foráneas
            $table->foreign('tipo_orden_id')->references('id')->on('parametros_temas')->onDelete('restrict');
            
            $table->foreign('user_create_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('user_update_id')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordenes');
    }
};
