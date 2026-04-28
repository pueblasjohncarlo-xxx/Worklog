<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Schema;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'audience',
        'user_id',
        'attachment',
        'original_filename',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipients(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_user', 'announcement_id', 'user_id')
            ->withTimestamps();
    }

    public static function recipientsTableExists(): bool
    {
        static $exists;

        if ($exists === null) {
            $exists = Schema::hasTable('announcement_user');
        }

        return $exists;
    }
}
