<?php

namespace App\Enums;

enum BookmarkArchiveEnum
{
    use Enum;

    case ALL;
    case ARCHIVED;
    case UNARCHIVED;
}
