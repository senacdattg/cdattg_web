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
            // Drop old jornada_trabajo_id column since we're using pivot table now
            $table->dropForeign(['jornada_trabajo_id']);
            $table->dropColumn('jornada_trabajo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instructors', function (Blueprint $table) {
            // Restore old column
            $table->foreignId('jornada_trabajo_id')->nullable()->after('centro_formacion_id')->constrained('jornadas_formacion')->onDelete('set null')->comment('Jornada de trabajo');
        });
    }
};

