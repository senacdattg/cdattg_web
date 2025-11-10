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
            if (!Schema::hasColumn('fichas_caracterizacion', 'fecha_inicio')) {
                $table->date('fecha_inicio')->nullable()->after('ficha');
            }
            if (!Schema::hasColumn('fichas_caracterizacion', 'fecha_fin')) {
                $table->date('fecha_fin')->nullable()->after('fecha_inicio');
            }
            if (!Schema::hasColumn('fichas_caracterizacion', 'total_horas')) {
                $table->integer('total_horas')->nullable()->after('fecha_fin');
            }
            if (!Schema::hasColumn('fichas_caracterizacion', 'ambiente_id')) {
                $table->foreignId('ambiente_id')->nullable()->after('fecha_fin')->constrained('ambientes');
            }
            if (!Schema::hasColumn('fichas_caracterizacion', 'modalidad_formacion_id')) {
                $table->foreignId('modalidad_formacion_id')->nullable()->after('ambiente_id')->constrained('parametros_temas');
            }
            if (!Schema::hasColumn('fichas_caracterizacion', 'sede_id')) {
                $table->foreignId('sede_id')->nullable()->after('modalidad_formacion_id')->constrained('sedes');
            }
            if (!Schema::hasColumn('fichas_caracterizacion', 'jornada_id')) {
                $table->foreignId('jornada_id')->nullable()->after('sede_id')->constrained('jornadas_formacion');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fichas_caracterizacion', function (Blueprint $table) {
            if (Schema::hasColumn('fichas_caracterizacion', 'fecha_inicio')) {
                $table->dropColumn('fecha_inicio');
            }
            if (Schema::hasColumn('fichas_caracterizacion', 'fecha_fin')) {
                $table->dropColumn('fecha_fin');
            }
            if (Schema::hasColumn('fichas_caracterizacion', 'total_horas')) {
                $table->dropColumn('total_horas');
            }
            if (Schema::hasColumn('fichas_caracterizacion', 'ambiente_id')) {
                $table->dropForeign(['ambiente_id']);
                $table->dropColumn('ambiente_id');
            }
            if (Schema::hasColumn('fichas_caracterizacion', 'modalidad_formacion_id')) {
                $table->dropForeign(['modalidad_formacion_id']);
                $table->dropColumn('modalidad_formacion_id');
            }
            if (Schema::hasColumn('fichas_caracterizacion', 'sede_id')) {
                $table->dropForeign(['sede_id']);
                $table->dropColumn('sede_id');
            }
            if (Schema::hasColumn('fichas_caracterizacion', 'jornada_id')) {
                $table->dropForeign(['jornada_id']);
                $table->dropColumn('jornada_id');
            }
        });
    }
};