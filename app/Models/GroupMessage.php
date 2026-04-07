<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMessage extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'group_chat_id',
        'sender_id',
        'body',
        'read_at',
        'attachment_path',
        'attachment_type',
        'attachment_name',
        'is_edited',
        'edited_by',
        'is_pinned',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'is_edited' => 'boolean',
        'is_pinned' => 'boolean',
    ];

    public function groupChat(): BelongsTo
    {
        return $this->belongsTo(GroupChat::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function editedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'edited_by');
    }

    public function isOwner($userId): bool
    {
        return $this->sender_id === $userId;
    }

    public function canEdit($userId): bool
    {
        return $this->isOwner($userId) && $this->created_at->diffInMinutes(now()) < 15;
    }

    public function canDelete($userId): bool
    {
        return $this->isOwner($userId) && $this->created_at->diffInMinutes(now()) < 60;
    }
}
