<?php

namespace App\Models\Prop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Modelo Request (Solicitud de información)
 *
 * Representa una solicitud o inquiry que un visitante envía sobre una propiedad.
 * Almacena datos de contacto (nombre, email, teléfono), el mensaje, la propiedad
 * de interés y opcionalmente el usuario logueado que envió la solicitud.
 */
class Request extends Model
{
    use HasFactory;

    /** Estados posibles del request para seguimiento (pendiente, contactado, cerrado). */
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_CLOSED = 'closed';

    protected $table = 'requests';

    /** Campos asignables en masa (mass assignment). */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'property_id',
        'user_id',
        'status',
    ];

    /** Relación: cada request pertenece a una propiedad. */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /** Relación: cada request puede pertenecer a un usuario (si está logueado). */
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
