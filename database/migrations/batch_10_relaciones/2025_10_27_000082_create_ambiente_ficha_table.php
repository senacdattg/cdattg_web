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
        if (!Schema::hasTable('ambientes') || !Schema::hasTable('fichas_caracterizacion')) {
            return;
        }

        if (Schema::hasTable('ambiente_ficha')) {
            return;
        }

        Schema::create('ambiente_ficha', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ambiente_id')->constrained('ambientes');
            $table->foreignId('ficha_id')->constrained('fichas_caracterizacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ambiente_ficha');
    }
};
