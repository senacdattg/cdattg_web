<?php

namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Seeder;
use Database\Seeders\Concerns\TruncatesTables;

class PaisSeeder extends Seeder
{
    use TruncatesTables;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->truncateModel(Pais::class);

        Pais::updateOrCreate(
            ['id' => 1],
            ['pais' => 'COLOMBIA']
        );
    }
}
