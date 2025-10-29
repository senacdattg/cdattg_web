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
            $table->foreignId('dias_de_formacion')->after('ficha');
            $table->foreignId('user_create_id')->after('dias_de_formacion')->constrained('users');
            $table->foreignId('user_edit_id')->after('user_create_id')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fichas_caracterizacion', function (Blueprint $table) {
            //
        });
    }
};
