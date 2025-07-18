<?php

namespace App\Repositories;

use App\Models\InstructorFichaCaracterizacion;

class InstructorFichaCaracterizacionRepository
{
    public function getInstructorFichaCaracterizacion($instructorId)
    {
        return InstructorFichaCaracterizacion::where('instructor_id', $instructorId)->get();
    }
}
