<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('persona_import_issues')) {
            return;
        }

        Schema::create('persona_import_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('persona_import_id')->constrained('persona_imports')->cascadeOnDelete();
            $table->unsignedInteger('row_number');
            $table->string('issue_type');
            $table->string('numero_documento')->nullable();
            $table->string('email')->nullable();
            $table->string('celular')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();

            $table->index(['persona_import_id', 'issue_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('persona_import_issues');
    }
};

