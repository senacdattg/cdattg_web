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
        Schema::table('fichas_caracterizacion', function (Blueprint $table) {
            $table->date('fecha_inicio')->nullable()->after('ficha');
            $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            $table->foreignId('ambiente_id')->nullable()->after('fecha_fin')->constrained('ambientes');
            $table->foreignId('modalidad_formacion_id')->nullable()->after('ambiente_id')->constrained('modalidades_formacion');
            $table->foreignId('sede_id')->nullable()->after('modalidad_formacion_id')->constrained('sedes');
            $table->foreignId('jornada_id')->nullable()->after('sede_id')->constrained('jornadas_formacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fichas_caracterizacion', function (Blueprint $table) {
            $table->dropColumn('fecha_inicio');
            $table->dropColumn('fecha_fin');
            $table->dropForeign(['ambiente_id']);
            $table->dropForeign(['modalidad_formacion_id']);
            $table->dropColumn('ambiente_id');
            $table->dropColumn('modalidad_formacion_id');
            $table->dropForeign(['sede_id']);
            $table->dropColumn('sede_id');
        });
    }
};
