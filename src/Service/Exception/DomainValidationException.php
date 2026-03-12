<?php

declare(strict_types=1);

namespace App\Service\Exception;

/**
 * Внутренние бизнес-правила не позволили принять переданные данные
 */
abstract class DomainValidationException extends DomainException
{
}