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
        Schema::table('entrada_salidas', function (Blueprint $table) {
            $table->foreignId('ficha_caracterizacion_id')->constrained('fichas_caracterizacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrada_salidas', function (Blueprint $table) {
            //
        });
    }
};
