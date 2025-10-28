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
        Schema::create('entrada_salidas', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->foreignId('instructor_user_id')->constrained('users');
            $table->string('aprendiz');
            $table->time('entrada');
            $table->time('salida')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrada_salidas');
    }
};
