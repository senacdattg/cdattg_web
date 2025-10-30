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
        // Si la tabla ya fue creada manualmente en MySQL, no volver a crearla
        if (!Schema::hasTable('aspirantes_complementarios')) {
            Schema::create('aspirantes_complementarios', function (Blueprint $table) {
                $table->id();
                $table->foreignId('persona_id')
                    ->constrained('personas')
                    ->cascadeOnDelete()
                    ->cascadeOnUpdate();
                $table->text('observaciones')->nullable();
                $table->boolean('estado')->default(1);
                $table->timestamps();

                $table->unique('persona_id', 'aspirantes_complementarios_persona_id_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aspirantes_complementarios');
    }
};
