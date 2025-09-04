<?php

namespace App\Enum;

enum QuotationStatusEnum : string
{
    case DRAFT      = 'brouillon';
    case SENT       = 'envoyé';
    case ACCEPTED   = 'acccepté';
    case REJECTED   = 'refusé';
    case EXPIRED    = 'expiré';
    case CANCELLED  = 'annulé';
}
