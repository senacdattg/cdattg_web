<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCaracterizacionIdToPersonasTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Esta columna ya no se usa, ahora se maneja con la tabla persona_caracterizacion
        // Se mantiene la migración por compatibilidad pero no modifica la tabla
        return;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropForeign(['caracterizacion_id']);
            $table->dropColumn('caracterizacion_id');
        });
    }
}