<?php

namespace App\Enums;

enum BookmarkDeleteEnum
{
    use Enum;

    case ALL;
    case DELETED;
    case UNDELETED;
}
