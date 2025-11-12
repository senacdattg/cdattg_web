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
        Schema::table('programas_formacion', function (Blueprint $table) {
            if (!Schema::hasColumn('programas_formacion', 'horas_totales')) {
                $table->unsignedInteger('horas_totales')
                    ->default(0)
                    ->after('status');
            }

            if (!Schema::hasColumn('programas_formacion', 'horas_etapa_lectiva')) {
                $table->unsignedInteger('horas_etapa_lectiva')
                    ->default(0)
                    ->after('horas_totales');
            }

            if (!Schema::hasColumn('programas_formacion', 'horas_etapa_productiva')) {
                $table->unsignedInteger('horas_etapa_productiva')
                    ->default(0)
                    ->after('horas_etapa_lectiva');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('programas_formacion', function (Blueprint $table) {
            if (Schema::hasColumn('programas_formacion', 'horas_etapa_productiva')) {
                $table->dropColumn('horas_etapa_productiva');
            }

            if (Schema::hasColumn('programas_formacion', 'horas_etapa_lectiva')) {
                $table->dropColumn('horas_etapa_lectiva');
            }

            if (Schema::hasColumn('programas_formacion', 'horas_totales')) {
                $table->dropColumn('horas_totales');
            }
        });
    }
};

