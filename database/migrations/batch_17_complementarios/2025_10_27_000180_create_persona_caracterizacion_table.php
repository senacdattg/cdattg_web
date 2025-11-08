<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('persona_caracterizacion')) {
            return;
        }

        Schema::create('persona_caracterizacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_id')
                ->constrained('personas')
                ->cascadeOnDelete();
            $table->foreignId('categoria_caracterizacion_id')
                ->constrained('categorias_caracterizacion_complementarios')
                ->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['persona_id', 'categoria_caracterizacion_id'], 'persona_caracterizacion_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona_caracterizacion');
    }
};

