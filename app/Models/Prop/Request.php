<?php

namespace App\Models\Prop;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Request extends Model
{
    use HasFactory;

    /** Estados del request. */
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_CLOSED = 'closed';

    protected $table = 'requests';
    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'property_id',
        'user_id',
        'status',
    ];
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
