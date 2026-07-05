<?php

namespace App\Models;

use Database\Factories\BookmarkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bookmark extends Model
{
    const CREATED_AT = null;

    /** @use HasFactory<BookmarkFactory> */
    use HasFactory;

    protected $table = 'bookmarks';

    protected $fillable = [
        'url_id',
        'collection',
        'note',
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

    public function url(): BelongsTo
    {
        return $this->belongsTo(Url::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
