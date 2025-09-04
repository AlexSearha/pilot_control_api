<?php

namespace App\Enum;

enum ProjectStatusEnum : string
{
    case PLANNED        = 'plannifié';
    case IN_PROGRESS    = 'en cours';
    case ON_HOLD        = 'en pause';
    case COMPLETED      = 'terminé';
    case CANCELLED      = 'annulé';
    case ARCHIVED       = 'archivé';
}
