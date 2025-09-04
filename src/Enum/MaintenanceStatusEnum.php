<?php

namespace App\Enum;

enum MaintenanceStatusEnum : string
{
    case PLANNED        = 'plannifiée';
    case IN_PROGRESS    = 'en cours';
    case DONE           = 'terminée';
    case CANCELLED      = 'annulée';
}
