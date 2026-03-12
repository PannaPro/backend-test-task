<?php

declare(strict_types=1);

namespace App\Service\Exception;

/**
 * Сущность не найдена
 */
class DomainNotFoundException extends DomainException
{
    public function getTitle(): string
    {
        return "Entity does not exist";
    }

    public static function notFound(): self
    {
        return new self("Not found");
    }
}