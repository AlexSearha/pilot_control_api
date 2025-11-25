<?php

namespace App\Enum;

enum TicketPriorityEnum: string
{
    case LOW     = 'Faible';
    case MEDIUM  = 'Moyenne';
    case HIGH    = 'Haute';
    case URGENT  = 'Critique';
}
