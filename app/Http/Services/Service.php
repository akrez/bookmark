<?php

namespace App\Http\Services;

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
