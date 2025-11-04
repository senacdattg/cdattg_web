<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (!Schema::hasColumn('productos', 'codigo_barras_sena')) {
                $table->string('codigo_barras_sena', 11)->nullable()->unique()->after('codigo_barras');
            }
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            if (Schema::hasColumn('productos', 'codigo_barras_sena')) {
                $table->dropUnique('productos_codigo_barras_sena_unique');
                $table->dropColumn('codigo_barras_sena');
            }
        });
    }
};


