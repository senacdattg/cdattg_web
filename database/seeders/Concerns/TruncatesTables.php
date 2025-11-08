<?php

namespace Database\Seeders\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait TruncatesTables
{
    protected function truncateModel(string $modelClass): void
    {
        /** @var Model $model */
        $model = new $modelClass();

        Schema::disableForeignKeyConstraints();
        $model->newQuery()->truncate();
        Schema::enableForeignKeyConstraints();
    }

    protected function truncateTable(string $table): void
    {
        Schema::disableForeignKeyConstraints();
        DB::table($table)->truncate();
        Schema::enableForeignKeyConstraints();
    }
}



