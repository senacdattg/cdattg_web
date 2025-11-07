<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('ALTER TABLE personas MODIFY email VARCHAR(255) NULL');
        DB::statement('ALTER TABLE personas MODIFY celular VARCHAR(255) NULL');
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('UPDATE personas SET email = IFNULL(email, CONCAT("PENDIENTE-EMAIL-", id, "@placeholder.local")) WHERE email IS NULL');
        DB::statement('UPDATE personas SET celular = IFNULL(celular, CONCAT("000", id)) WHERE celular IS NULL');
        DB::statement('ALTER TABLE personas MODIFY email VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE personas MODIFY celular VARCHAR(255) NULL');
    }
};

