<?php

declare(strict_types=1);

namespace App\Service\Exception;

/**
 * DomainException (базовый из PHP может пересекаться с другими библиотеками)
 */
abstract class DomainException extends \DomainException
{
    protected function __construct(string $detail) {
        parent::__construct($detail);
    }

    /**
     * Значение для title из RFC 7807 стандарта (для ошибок)
     *
     * @return string
     */
    abstract public function getTitle(): string;

    /**
     * Значение для detail из RFC 7807 стандарта (для ошибок)
     *
     * @return string
     */
    final public function getDetail(): string
    {
        return parent::getMessage();
    }
}