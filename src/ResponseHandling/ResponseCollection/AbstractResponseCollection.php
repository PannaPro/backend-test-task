<?php

declare(strict_types=1);

namespace App\ResponseHandling\ResponseCollection;

abstract class AbstractResponseCollection implements ResponseCollectionInterface
{
    protected array $items = [];

    protected array $meta = [];

    protected array $links = [];

    /**
     * @return mixed[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return mixed[]
     */
    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @return mixed[]
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    public function addMeta(string $key, mixed $value): ResponseCollectionInterface
    {
        $this->meta[$key] = $value;

        return $this;
    }

    public function addLink(string $key, string $value): ResponseCollectionInterface
    {
        $this->links[$key] = $value;

        return $this;
    }
}