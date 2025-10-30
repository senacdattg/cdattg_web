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
        Schema::create('persona_caracterizacion', function (Blueprint $table) {
            // Claves foráneas
            $table->foreignId('persona_id')
                ->constrained('personas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->foreignId('categoria_id')
                ->constrained('categorias_caracterizacion_complementarios')
                ->restrictOnDelete()
                ->cascadeOnUpdate();

            // Datos de caracterización asociados a la relación (flexible)
            $table->json('caracterizacion')->nullable();
            $table->text('observaciones')->nullable();

            // Tiempos
            $table->timestamp('asignado_en')->useCurrent();
            $table->timestamps();

            // PK compuesta para garantizar unicidad por par persona-categoría
            $table->primary(['persona_id', 'categoria_id'], 'persona_caracterizacion_pk');

            // Índices auxiliares
            $table->index('categoria_id', 'persona_caracterizacion_categoria_idx');
            $table->index('persona_id', 'persona_caracterizacion_persona_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('persona_caracterizacion');
    }
};
