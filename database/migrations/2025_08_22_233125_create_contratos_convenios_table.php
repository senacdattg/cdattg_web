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
        Schema::create('contratos_convenios', function (Blueprint $table) {
            $table->id();

            $table->string('name')->unique();
            $table->unsignedBigInteger('proveedor_id');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->unsignedBigInteger('estado_id');
            $table->unsignedBigInteger('user_create_id');
            $table->unsignedBigInteger('user_update_id');

            $table->timestamps();

            $table->foreign('proveedor_id')->references('id')->on('proveedores')->onDelete('restrict');
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
        Schema::dropIfExists('contratos_convenios');
    }
};
