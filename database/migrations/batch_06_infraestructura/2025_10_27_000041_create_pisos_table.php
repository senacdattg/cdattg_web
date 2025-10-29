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
        Schema::create('pisos', function (Blueprint $table) {
            $table->id();
            $table->string('piso');
            $table->foreignId('bloque_id')->constrained('bloques');
            $table->foreignId('user_create_id')->constrained('users');
            $table->foreignId('user_edit_id')->constrained('users');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->unique(['piso', 'bloque_id'], 'piso_uk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pisos');
    }
};
