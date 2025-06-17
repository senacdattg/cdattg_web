<?php

namespace Database\Seeders;

use App\Models\Instructor;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Instructor::create([
            'id' => 1,
            'persona_id' => 3,
            'regional_id' => 1,
        ]);
    }

    /**
     * Get the personaInstructor that owns the InstructorSeeder
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function personaInstructor(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id', 'id');
    }
}