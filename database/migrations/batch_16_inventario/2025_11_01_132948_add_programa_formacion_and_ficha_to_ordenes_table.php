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
        Schema::table('ordenes', function (Blueprint $table) {
            $table->unsignedBigInteger('programa_formacion_id')->nullable()->after('tipo_orden_id');
            $table->string('ficha', 7)->nullable()->after('programa_formacion_id');

            $table->foreign('programa_formacion_id')->references('id')->on('programas_formacion')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordenes', function (Blueprint $table) {
            $table->dropForeign(['programa_formacion_id']);
            
            $table->dropColumn(['programa_formacion_id', 'ficha']);
        }); 
    }
};
