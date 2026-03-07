<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PonderacionAcabado extends Model
{
    protected $table = 'ponderacion_acabados';

    protected $fillable = [
        'tipo',
        'ponderacion',
    ];

    protected function casts(): array
    {
        return ['ponderacion' => 'integer'];
    }
}
