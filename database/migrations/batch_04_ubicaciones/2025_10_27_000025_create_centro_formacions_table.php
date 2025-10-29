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
        Schema::create('centro_formacions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('regional_id')->constrained();
            $table->string('nombre');
            $table->string('telefono');
            $table->string('direccion');
            $table->string('web');
            $table->boolean('status')->default(1);
            $table->foreignId('user_create_id')->constrained('users');
            $table->foreignId('user_update_id')->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('centro_formacions');
    }
};
