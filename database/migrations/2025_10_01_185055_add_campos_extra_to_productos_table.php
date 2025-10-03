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
        Schema::table('productos', function (Blueprint $table) {
            $table->unsignedBigInteger('categoria_id')->nullable()->after('estado_producto_id');
            $table->unsignedBigInteger('marca_id')->nullable()->after('categoria_id');
            $table->unsignedBigInteger('contrato_convenio_id')->nullable()->after('marca_id');
            $table->unsignedBigInteger('ambiente_id')->nullable()->after('contrato_convenio_id');

            $table->foreign('categoria_id')->references('id')->on('categorias')->onDelete('restrict');
            $table->foreign('marca_id')->references('id')->on('marcas')->onDelete('restrict');
            $table->foreign('contrato_convenio_id')->references('id')->on('contratos_convenios')->onDelete('restrict');
            $table->foreign('ambiente_id')->references('id')->on('ambientes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropForeign(['marca_id']);
            $table->dropForeign(['contrato_convenio_id']);
            $table->dropForeign(['ambiente_id']);

            $table->dropColumn(['categoria_id','marca_id','contrato_convenio_id','ambiente_id']);
        });
    }
};
