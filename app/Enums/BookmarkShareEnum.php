<?php

namespace App\Enums;

enum BookmarkShareEnum
{
    use Enum;

    case ALL;
    case SHARED;
    case UNSHARED;
}
