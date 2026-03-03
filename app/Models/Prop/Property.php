<?php

namespace App\Models\Prop;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    /** Tipos de oferta para filtros (venta, renta, arrendamiento). */
    public const OFFER_SALE = 'sale';
    public const OFFER_RENT = 'rent';
    public const OFFER_LEASE = 'lease';

    /** Tipos de inmueble (condo, comercial, terreno, casa, etc.). */
    public const HOME_TYPE_CONDO = 'condo';
    public const HOME_TYPE_COMMERCIAL = 'commercial';
    public const HOME_TYPE_LAND = 'land';
    public const HOME_TYPE_HOUSE = 'house';
    public const HOME_TYPE_APARTMENT = 'apartment';

    /** Estados del listado. */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'currency',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'latitude',
        'longitude',
        'image',
        'status',
        'offer_type',
        'beds',
        'baths',
        'sqft',
        'home_type',
        'year_built',
        'price_per_sqft',
        'featured',
        'agent_id',
    ];

    /**
     * Atributos que deben ser casteados.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'price_per_sqft' => 'decimal:2',
            'latitude' => 'float',
            'longitude' => 'float',
            'beds' => 'integer',
            'baths' => 'integer',
            'sqft' => 'integer',
            'year_built' => 'integer',
            'featured' => 'boolean',
        ];
    }

    public $timestamps = true;

    /**
     * Imágenes de la propiedad.
     */
    public function images(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(PropImage::class)->orderBy('order');
    }

    /**
     * Agente (usuario) asignado al inmueble.
     */
    public function agent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    /**
     * Scope: solo propiedades destacadas (para carousel/home).
     */
    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    /**
     * Scope: solo propiedades activas.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }
}
