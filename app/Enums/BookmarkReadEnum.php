<?php

namespace App\Enums;

enum BookmarkReadEnum
{
    use Enum;

    case ALL;
    case READ;
    case UNREAD;
}
