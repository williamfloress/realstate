<?php

namespace App\Models;

use App\Models\Admin\Admin;
use App\Models\Prop\Property;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AgentApplication extends Model
{
    use HasFactory;

    protected $table = 'agent_applications';

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'phone',
        'license_number',
        'bio',
        'real_estate_certificate',
        'id_document',
        'other_documents',
        'message',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
            'other_documents' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Admin::class, 'reviewed_by');
    }
}
