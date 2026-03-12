<?php

declare(strict_types=1);

namespace App\Service\Exception;

final class PaymentFailedException extends DomainExternalException
{
    public function getTitle(): string
    {
        return 'Payment failed';
    }

    public static function because(string $detail): self
    {
        return new self($detail);
    }
}
