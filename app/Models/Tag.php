<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Bookmark::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
