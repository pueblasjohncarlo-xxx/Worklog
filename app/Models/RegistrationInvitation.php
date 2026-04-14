<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'role',
        'company_id',
        'invited_by_user_id',
        'token_hash',
        'expires_at',
        'accepted_at',
        'revoked_at',
        'revoked_by_user_id',
        'metadata',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
        'revoked_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by_user_id');
    }

    public function revokedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by_user_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function getStatusAttribute(): string
    {
        if (! is_null($this->revoked_at)) {
            return 'revoked';
        }

        if (! is_null($this->accepted_at)) {
            return 'accepted';
        }

        if ($this->expires_at?->isPast()) {
            return 'expired';
        }

        return 'pending';
    }
}
