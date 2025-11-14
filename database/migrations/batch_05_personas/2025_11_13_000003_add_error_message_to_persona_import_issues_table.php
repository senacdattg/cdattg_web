<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('persona_import_issues', function (Blueprint $table) {
            if (! Schema::hasColumn('persona_import_issues', 'error_message')) {
                $table->text('error_message')->nullable()->after('celular');
            }
        });
    }

    public function down(): void
    {
        Schema::table('persona_import_issues', function (Blueprint $table) {
            if (Schema::hasColumn('persona_import_issues', 'error_message')) {
                $table->dropColumn('error_message');
            }
        });
    }
};
