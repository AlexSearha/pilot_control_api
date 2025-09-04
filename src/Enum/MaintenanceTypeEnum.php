<?php

namespace App\Enum;

enum MaintenanceTypeEnum : string
{
    case PREVENTIVE     = 'préventive';
    case CORRECTIVE     = 'currative';
    case PREDICTIVE     = 'prédictive';
}
