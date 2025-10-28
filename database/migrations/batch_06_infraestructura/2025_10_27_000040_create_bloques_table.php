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
        Schema::create('bloques', function (Blueprint $table) {
            $table->id();
            $table->string('bloque');
            $table->foreignId('sede_id')->constrained('sedes');
            $table->foreignId('user_create_id')->constrained('users');
            $table->foreignId('user_edit_id')->constrained('users');
            $table->boolean('status')->default(1);
            $table->timestamps();

            $table->unique(['bloque', 'sede_id'], 'bloque_uk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bloques');
    }
};
