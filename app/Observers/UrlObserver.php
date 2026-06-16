<?php

namespace App\Observers;

use App\Models\Url;
use App\Support\Helper;

class UrlObserver
{
    public function creating(Url $url): void
    {
        $this->setBaseUrl($url);
    }

    public function updating(Url $url): void
    {
        if ($url->isDirty('url')) {
            $this->setBaseUrl($url);
        }
    }

    protected function setBaseUrl(Url $url): void
    {
        $url->base_url = app(Helper::class)->extractBaseUrl($url?->url);
    }
}
