<?php

namespace App\Models\Prop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo SavedProperties (propiedades guardadas / favoritos)
 *
 * Representa la relación entre un usuario y una propiedad que ha guardado como favorita.
 * Solo almacena user_id y property_id; los datos de la propiedad (título, precio,
 * dirección, imagen) se obtienen mediante $savedProperty->property.
 */
class SavedProperties extends Model
{
    use HasFactory;

    protected $table = 'saved_properties';

    /** Campos asignables en masa (mass assignment). */
    protected $fillable = [
        'user_id',
        'property_id',
    ];

    /** Relación: cada registro pertenece a un usuario. */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /** Relación: cada registro pertenece a una propiedad (acceso a title, price, address, image, etc.). */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
