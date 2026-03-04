<?php

namespace App\Models\Prop;

use Illuminate\Database\Eloquent\Model;

/**
 * Modelo HomeType (tipo de inmueble)
 *
 * Catálogo de tipos: condo, land, commercial, house, apartment.
 * home_type = slug para URLs; name = nombre para mostrar en UI.
 */
class HomeType extends Model
{
    protected $table = 'home_types';

    protected $fillable = [
        'home_type',
        'name',
        'order',
    ];

    protected function casts(): array
    {
        return ['order' => 'integer'];
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }
}
