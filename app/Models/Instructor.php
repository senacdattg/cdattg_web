<?php

namespace App\Models;

use App\Http\Controllers\FichaCaracterizacionController;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $table = 'instructors';

    protected $fillable = [
        'persona_id',
        'regional_id',
    ];


    public function persona()
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }
    // public function fichasCaracterizacion()
    // {
    //     return $this->belongsTo(FichaCaracterizacionController::class. 'instructor_asignado');
    // }
    public function regional()
    {
        return $this->belongsTo(Regional::class, 'regional_id');
    }
    public function fichas()
    {
        return $this->hasMany(FichaCaracterizacion::class, 'instructor_id');
    }

    public function caracterizacionProgramas()
    {
        return $this->hasMany(CaracterizacionPrograma::class, 'instructor_persona_id');
    }


    public function user()
    {
        return $this->hasOne(User::class, 'persona_id');
    }
}
