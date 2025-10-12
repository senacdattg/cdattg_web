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
        Schema::table('aspirantes_complementarios', function (Blueprint $table) {
            $table->foreignId('complementario_id')
                ->nullable()
                ->constrained('complementarios_ofertados')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aspirantes_complementarios', function (Blueprint $table) {
            $table->dropForeign(['complementario_id']);
        });
    }
};
