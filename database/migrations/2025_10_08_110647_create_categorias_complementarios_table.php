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
        Schema::create('categorias_caracterizacion_complementarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 191);
            $table->string('slug', 191);
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique('nombre', 'categorias_caracterizacion_complementarios_nombre_unique');
            $table->unique('slug', 'categorias_caracterizacion_complementarios_slug_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias_caracterizacion_complementarios');
    }
};
