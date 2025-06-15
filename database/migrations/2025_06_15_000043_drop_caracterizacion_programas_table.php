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
        Schema::dropIfExists('caracterizacion_programas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('caracterizacion_programas', function (Blueprint $table) {
            //
        });
    }
};
