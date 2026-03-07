<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    public const ROLE_USER = 'user';
    public const ROLE_AGENT = 'agent';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /** Relación: un usuario puede tener muchas solicitudes de información. */
    public function requests()
    {
        return $this->hasMany(\App\Models\Prop\Request::class);
    }

    /** Relación: propiedades guardadas en favoritos por el usuario. */
    public function savedProperties()
    {
        return $this->hasMany(\App\Models\Prop\SavedProperties::class);
    }

    public function agentApplication()
    {
        return $this->hasOne(AgentApplication::class);
    }

    public function properties()
    {
        return $this->hasMany(\App\Models\Prop\Property::class, 'agent_id');
    }

    public function isAgent(): bool
    {
        return ($this->role ?? '') === self::ROLE_AGENT;
    }
}
