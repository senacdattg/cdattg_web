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
        Schema::table('proveedores', function (Blueprint $table) {
            $table->string('telefono', 10)->nullable()->after('email');
            $table->string('direccion')->nullable()->after('telefono');
            $table->unsignedBigInteger('municipio_id')->nullable()->after('direccion');
            $table->string('contacto', 100)->nullable()->after('municipio_id');
            $table->unsignedBigInteger('estado_id')->nullable()->after('contacto');

            $table->foreign('municipio_id')->references('id')->on('municipios')->onDelete('restrict');
            $table->foreign('estado_id')->references('id')->on('parametros_temas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proveedores', function (Blueprint $table) {
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['estado_id']);

            $table->dropColumn([
                'telefono',
                'direccion',
                'municipio_id',
                'contacto',
                'estado_id',
            ]);
        });
    }
};
