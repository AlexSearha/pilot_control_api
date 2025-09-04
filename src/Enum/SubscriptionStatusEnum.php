<?php


namespace App\Enum;

enum SubscriptionStatusEnum: string
{
    case PENDING    = 'pending';
    case ACTIVE     = 'active';
    case CANCELLED  = 'cancelled';
    case EXPIRED    = 'expired';
    case SUSPENDED  = 'suspended';
}
