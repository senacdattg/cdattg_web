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
        Schema::table('instructors', function (Blueprint $table) {
            // Drop old tipo_vinculacion column
            $table->dropIndex('idx_instructors_tipo_vinculacion');
            $table->dropColumn('tipo_vinculacion');
            
            // Add new tipo_vinculacion_id as foreign key to parametros_temas
            $table->foreignId('tipo_vinculacion_id')->nullable()->after('regional_id')->constrained('parametros_temas')->onDelete('set null')->comment('Tipo de vinculación (parámetro_tema)');
            $table->index('tipo_vinculacion_id', 'idx_instructors_tipo_vinculacion_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // Drop new column
            $table->dropIndex('idx_instructors_tipo_vinculacion_id');
            $table->dropForeign(['tipo_vinculacion_id']);
            $table->dropColumn('tipo_vinculacion_id');
            
            // Restore old column
            $table->string('tipo_vinculacion', 50)->nullable()->after('regional_id')->comment('Tipo de vinculación: planta, contratista, apoyo a la formación');
            $table->index('tipo_vinculacion', 'idx_instructors_tipo_vinculacion');
        });
    }
};

