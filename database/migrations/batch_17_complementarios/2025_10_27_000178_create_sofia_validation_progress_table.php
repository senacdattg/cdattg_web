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
        Schema::create('sofia_validation_progress', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('complementario_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->integer('total_aspirantes')->default(0);
            $table->integer('processed_aspirantes')->default(0);
            $table->integer('successful_validations')->default(0);
            $table->integer('failed_validations')->default(0);
            $table->json('errors')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('complementario_id')->references('id')->on('complementarios_ofertados');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sofia_validation_progress');
    }
};
