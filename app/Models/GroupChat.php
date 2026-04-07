<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GroupChat extends Model
{
    protected $fillable = [
        'name',
        'description',
        'created_by',
        'company_id',
        'chat_type',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'group_chat_members')
            ->withPivot('joined_at', 'left_at');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(GroupMessage::class);
    }

    public function latestMessage()
    {
        return $this->messages()->latest()->first();
    }

    public function isMember($userId): bool
    {
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function addMember($userId)
    {
        if (!$this->isMember($userId)) {
            $this->members()->attach($userId, ['joined_at' => now()]);
        }
    }

    public function removeMember($userId)
    {
        $this->members()->updateExistingPivot($userId, ['left_at' => now()]);
    }
}
