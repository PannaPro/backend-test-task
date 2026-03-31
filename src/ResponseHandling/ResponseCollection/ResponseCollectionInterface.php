<?php

declare(strict_types=1);

namespace App\ResponseHandling\ResponseCollection;

interface ResponseCollectionInterface
{
    public function getItems(): array;

    public function getMeta(): array;

    public function addMeta(string $key, mixed $value): self;

    public function getLinks(): array;

    public function addLink(string $key, string $value): self;
}