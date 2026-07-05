<?php

namespace App\Models;

use App\Observers\UrlObserver;
use Database\Factories\UrlFactory;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    /** @use HasFactory<UrlFactory> */
    use HasFactory, HasTimestamps;

    protected $table = 'urls';

    protected $fillable = [
        'url',
        'base_url',
        'title',
        'description',
        'favicon',
        'fetched_at',
    ];

    protected static function booted(): void
    {
        Url::observe(UrlObserver::class);
    }
}
