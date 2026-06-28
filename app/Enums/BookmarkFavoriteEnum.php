<?php

namespace App\Enums;

enum BookmarkFavoriteEnum
{
    use Enum;

    case ALL;
    case FAVORITED;
    case UNFAVORITED;
}
