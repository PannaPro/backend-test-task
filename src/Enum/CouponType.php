<?php

namespace App\Enum;

enum CouponType: string
{
    case FIXED_DISCOUNT = 'fixed discount';
    case PERCENTAGE_OF_PURCHASE = 'percentage of purchase';
}