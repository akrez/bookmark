<?php

namespace App\Services;

class Service
{
    /**
     * @return static
     */
    public static function new()
    {
        return app(static::class);
    }
}
