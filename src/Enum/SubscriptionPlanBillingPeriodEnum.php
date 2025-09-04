<?php

namespace App\Enum;

enum SubscriptionPlanBillingPeriodEnum: string
{
    case MONTHLY    = 'mensuel';
    case YEARLY     = 'annuel';
}
