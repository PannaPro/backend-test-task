<?php

namespace App\Service\Payment;

interface PaymentGatewayInterface
{
    public function supports(string $processor): bool;

    public function canCharge(int $amountInCents): bool;

    public function charge(int $amountInCents): void;
}
