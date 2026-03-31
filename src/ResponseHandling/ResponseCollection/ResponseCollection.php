<?php

declare(strict_types=1);

namespace App\ResponseHandling\ResponseCollection;

final class ResponseCollection extends AbstractResponseCollection
{
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
}