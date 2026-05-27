<?php

namespace App\Observers;

use App\Models\Bookmark;
use App\Support\Helper;

class BookmarkObserver
{
    public function creating(Bookmark $bookmark): void
    {
        $this->setBaseUrl($bookmark);
    }

    public function updating(Bookmark $bookmark): void
    {
        if ($bookmark->isDirty('url')) {
            $this->setBaseUrl($bookmark);
        }
    }

    protected function setBaseUrl(Bookmark $bookmark): void
    {
        $bookmark->base_url = app(Helper::class)->extractBaseUrl($bookmark?->url);
    }
}
