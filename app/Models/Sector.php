<?php

namespace App\Models;

use App\Models\Prop\Property;
use Illuminate\Database\Eloquent\Model;

class Sector extends Model
{
    protected $table = 'sectores';

    protected $fillable = [
        'nombre',
        'latitud',
        'longitud',
    ];

    protected function casts(): array
    {
        return [
            'latitud' => 'float',
            'longitud' => 'float',
        ];
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
