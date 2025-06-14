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
        Schema::dropIfExists('tipos_programas');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('tipos_programas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 255)->nullable()->unique();
            $table->timestamps();
        });
    }
};
