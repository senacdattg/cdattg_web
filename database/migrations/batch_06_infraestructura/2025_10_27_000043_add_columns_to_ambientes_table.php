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
        Schema::table('ambientes', function (Blueprint $table) {
            $table->string('title')->after('id');
            $table->foreignId('piso_id')->constrained('pisos')->after('title');
            $table->foreignId('user_create_id')->constrained('users')->after('piso_id');
            $table->foreignId('user_edit_id')->constrained('users')->after('user_create_id');
            $table->boolean('status')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ambientes', function (Blueprint $table) {
            //
        });
    }
};
