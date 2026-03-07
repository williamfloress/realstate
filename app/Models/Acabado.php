<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acabado extends Model
{
    public const TIPO_PISO = 'piso';
    public const TIPO_COCINA = 'cocina';
    public const TIPO_BANO = 'bano';

    protected $table = 'acabados';

    protected $fillable = [
        'tipo',
        'nombre',
        'puntaje',
    ];

    protected function casts(): array
    {
        return ['puntaje' => 'integer'];
    }

    public function scopePiso($query)
    {
        return $query->where('tipo', self::TIPO_PISO);
    }

    public function scopeCocina($query)
    {
        return $query->where('tipo', self::TIPO_COCINA);
    }

    public function scopeBano($query)
    {
        return $query->where('tipo', self::TIPO_BANO);
    }
}
