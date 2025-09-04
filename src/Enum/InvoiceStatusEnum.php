<?php

namespace App\Enum;

enum InvoiceStatusEnum : string
{
    case DRAFT              = 'brouillon';
    case SENT               = 'envoyée';
    case PARTIALLY_PAID     = 'partiellement payée';
    case PAID               = 'payée intégrallement';
    case CANCELLED          = 'annulée';
    case OVERDUE            = 'non payée apres la date limite';
}
