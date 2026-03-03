<?php

namespace App\Models\Prop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo de imagen de propiedad. Cada registro representa una foto asociada a un inmueble.
 */
class PropImage extends Model
{
    use HasFactory;

    /** Nombre de la tabla en la base de datos. */
    protected $table = 'prop_images';

    /** Campos asignables en masa (create/update). */
    protected $fillable = [
        'property_id',  // ID de la propiedad a la que pertenece
        'path',         // Nombre/ruta del archivo (ej: img_1.jpg)
        'caption',      // Descripción opcional de la imagen
        'order',        // Orden de visualización en el carrusel
    ];

    /** Convierte 'order' a entero al leer/escribir. */
    protected function casts(): array
    {
        return [
            'order' => 'integer',
        ];
    }

    /**
     * Propiedad a la que pertenece esta imagen.
     */
    public function property(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
