<?php

declare(strict_types=1);

namespace App\Service\Exception;

/**
 * Конфликт текущего состояния/данных при попытке выполнить операцию (логический конфликт или дублирование)
 * - Попытка создать дубликат
 * - Повторное выполнение операции (отменить уже отменённый заказ)
 */
abstract class DomainConflictException extends DomainException
{
}