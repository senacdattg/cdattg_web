<?php

namespace Database\Seeders;

use App\Models\Regional;
use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\TruncatesTables;

class RegionalSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(Regional::class);

        Regional::create([
            'nombre' => 'GUAVIARE',
            'departamento_id' => 95,
            'user_create_id' => '1',
            'user_edit_id' => '1',
            'status' => '1',
        ]);
    }
}
