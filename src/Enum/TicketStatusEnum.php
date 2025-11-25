<?php

namespace App\Enum;

enum TicketStatusEnum: string
{
    case PENDING    = 'En attente';
    case ACTIVE     = 'En cours';
    case CANCELLED  = 'Annulé';
    case EXPIRED    = 'Expiré';
    case SUSPENDED  = 'Suspendu';
}
