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
        // Esta tabla ya no se usa, los datos se manejan en la tabla parametros con tema_id = 16
        // Se mantiene la migración por compatibilidad pero no crea la tabla
        return;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_caracterizacion_complementarios');
    }
};
