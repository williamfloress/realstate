<?php

namespace App\Models\Prop;

use App\Models\Acabado;
use App\Models\Sector;
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

    /** Estados del listado. */
    public const STATUS_DRAFT = 'draft';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_PAUSED = 'paused';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_SOLD = 'sold';
    public const STATUS_RENTED = 'rented';
    public const STATUS_RESERVED = 'reserved';

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
        'home_type_id',
        'year_built',
        'price_per_sqft',
        'featured',
        'agent_id',
        'closed_at',
        'reserved_at',
        'sector_id',
        'area_construccion_m2',
        'parqueos',
        'acabado_piso_id',
        'acabado_cocina_id',
        'acabado_bano_id',
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
            'parqueos' => 'integer',
            'area_construccion_m2' => 'decimal:2',
            'year_built' => 'integer',
            'featured' => 'boolean',
            'closed_at' => 'datetime',
            'reserved_at' => 'datetime',
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
     * Tipo de inmueble (condo, land, commercial, etc.).
     */
    public function homeType(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(HomeType::class);
    }

    /**
     * Sector/ubicación para AMC.
     */
    public function sector(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    /**
     * Acabado de piso.
     */
    public function acabadoPiso(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Acabado::class, 'acabado_piso_id');
    }

    /**
     * Acabado de cocina.
     */
    public function acabadoCocina(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Acabado::class, 'acabado_cocina_id');
    }

    /**
     * Acabado de baño.
     */
    public function acabadoBano(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Acabado::class, 'acabado_bano_id');
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

    /**
     * URL de la imagen principal. Soporta rutas legacy (assets/images/) y subidas (storage/properties/).
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        if (str_starts_with($this->image, 'properties/')) {
            return asset('storage/' . $this->image);
        }
        return asset('assets/images/' . $this->image);
    }
}
