<?php

namespace App\Repositories;

use App\Models\Instructor;

class InstructorRepository
{
    public function getInstructor($personaId)
    {
        return Instructor::where('persona_id', $personaId)->first();
    }
}