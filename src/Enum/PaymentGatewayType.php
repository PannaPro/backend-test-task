<?php

declare(strict_types=1);

namespace App\Enum;

enum PaymentGatewayType: string
{
    case PAYPAL = 'paypal';
    case STRIPE = 'stripe';

    /**
     * @return string[]
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}