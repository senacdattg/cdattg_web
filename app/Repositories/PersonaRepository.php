<?php

namespace App\Repositories;

use App\Models\Persona;

class PersonaRepository
{
    public function getPersona($user)
    {
        return Persona::where('id', $user->person_id)->first();
    }
}