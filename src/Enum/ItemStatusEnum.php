<?php

namespace App\Enum;

enum ItemStatusEnum: string
{
    case IN_STOCK           = 'en stock';
    case OUT_OF_STOCK       = 'rupture de stock';
    case UNDER_MAINNTENANCE = 'maintenance en cours';
    case RESERVED           = 'reservé';
    case DISCONTINUED       = 'arrété';
}
