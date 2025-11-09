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
        if (!Schema::hasTable('ambientes')) {
            return;
        }

        Schema::table('complementarios_ofertados', function (Blueprint $table) {
            if (!Schema::hasColumn('complementarios_ofertados', 'ambiente_id')) {
                $table->foreignId('ambiente_id')->nullable()->constrained('ambientes')->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complementarios_ofertados', function (Blueprint $table) {
            if (Schema::hasColumn('complementarios_ofertados', 'ambiente_id')) {
                $table->dropForeign(['ambiente_id']);
                $table->dropColumn('ambiente_id');
            }
        });
    }
};
