<?php

namespace App\Enum;

enum TwoFactorStatusEnum : string
{
    case PENDING    = 'PENDING';
    case VERIFIED   = 'VERIFIED';
    case DISABLED   = 'DISABLED';
}
