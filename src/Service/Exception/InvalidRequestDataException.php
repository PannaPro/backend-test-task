<?php

declare(strict_types=1);

namespace App\Service\Exception;

final class InvalidRequestDataException extends DomainValidationException
{
    public function getTitle(): string
    {
        return 'Invalid request data';
    }

    public static function because(string $detail): self
    {
        return new self($detail);
    }
}
