<?php

namespace App\Enum;

enum PaymentMethodEnum : string
{
    case CASH           = 'espece';
    case CARD           = 'carte bancaire';
    case TRANSFERT      = 'virement';
    case CHECK          = 'chèque';
}
