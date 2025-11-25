<?php

namespace App\Enum;

enum TicketCategoryEnum: string
{
    case INVOICE        = 'Facturation';
    case QUOTATION      = 'Devis';
    case ITEM           = 'Article / Produit';
    case STOCK          = 'Stock';
    case MAINTENANCE    = 'Maintenance';
    case CLIENT         = 'Client / CRM';
    case PROJECT        = 'Projet';
    case ORDER          = 'Commande';
    case SCHEDULING     = 'Planification';
    case OTHER          = 'Autre';
}
