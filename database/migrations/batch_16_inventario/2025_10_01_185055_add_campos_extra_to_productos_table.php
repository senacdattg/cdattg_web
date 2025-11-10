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
            if (!Schema::hasColumn('productos', 'categoria_id')) {
                $table->unsignedBigInteger('categoria_id')->nullable()->after('estado_producto_id');
                $table->foreign('categoria_id')->references('id')->on('parametros')->onDelete('restrict');
            }

            if (!Schema::hasColumn('productos', 'marca_id')) {
                $table->unsignedBigInteger('marca_id')->nullable()->after('categoria_id');
                $table->foreign('marca_id')->references('id')->on('parametros')->onDelete('restrict');
            }

            if (!Schema::hasColumn('productos', 'contrato_convenio_id')) {
                $table->unsignedBigInteger('contrato_convenio_id')->nullable()->after('marca_id');
                $table->foreign('contrato_convenio_id')->references('id')->on('contratos_convenios')->onDelete('restrict');
            }

            if (!Schema::hasColumn('productos', 'ambiente_id')) {
                $table->unsignedBigInteger('ambiente_id')->nullable()->after('contrato_convenio_id');
                $table->foreign('ambiente_id')->references('id')->on('ambientes')->onDelete('restrict');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'categoria_id')) {
                $table->dropForeign(['categoria_id']);
                $table->dropColumn('categoria_id');
            }

            if (Schema::hasColumn('productos', 'marca_id')) {
                $table->dropForeign(['marca_id']);
                $table->dropColumn('marca_id');
            }

            if (Schema::hasColumn('productos', 'contrato_convenio_id')) {
                $table->dropForeign(['contrato_convenio_id']);
                $table->dropColumn('contrato_convenio_id');
            }

            if (Schema::hasColumn('productos', 'ambiente_id')) {
                $table->dropForeign(['ambiente_id']);
                $table->dropColumn('ambiente_id');
            }
        });
    }
};
