<?php

namespace App\Models;

use App\Observers\BookmarkObserver;
use Database\Factories\BookmarkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Bookmark extends Model
{
    const CREATED_AT = null;

    /** @use HasFactory<BookmarkFactory> */
    use HasFactory;

    protected $table = 'bookmarks';

    protected $fillable = [
        'url',
        'base_url',
        'collection',
        'title',
        'description',
        'notes',
        'read_at',
        'archived_at',
        'shared_at',
        'favorited_at',
        'user_id',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'archived_at' => 'datetime',
        'shared_at' => 'datetime',
        'favorited_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        Bookmark::observe(BookmarkObserver::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
