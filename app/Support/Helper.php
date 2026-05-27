<?php

namespace App\Support;

class Helper
{
    public function extractBaseUrl(?string $url): ?string
    {
        return $url ? parse_url($url, PHP_URL_SCHEME).'://'.parse_url($url, PHP_URL_HOST) : null;
    }
}
