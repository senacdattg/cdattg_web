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
        Schema::create('fichas_caracterizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programa_formacion_id')->constrained('programas_formacion');
            $table->foreignId('instructor_id')->constrained('instructors');
            $table->bigInteger('ficha');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fichas_caracterizacion');
    }
};
