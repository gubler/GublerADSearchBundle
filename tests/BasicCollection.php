<?php

declare(strict_types=1);

namespace Gubler\ADSearchBundle\Test;

use Symfony\Component\Ldap\Adapter\CollectionInterface;
use Symfony\Component\Ldap\Entry;

final class BasicCollection implements CollectionInterface
{
    /**
     * @param Entry[] $entries
     */
    public function __construct(private array $entries)
    {
    }

    /**
     * @return Entry[]
     */
    public function toArray(): array
    {
        return $this->entries;
    }

    public function count(): int
    {
        return \count($this->entries);
    }

    public function getIterator(): \Traversable
    {
        if (0 === $this->count()) {
            return;
        }

        foreach ($this->entries as $entry) {
            yield $entry;
        }
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset($this->entries[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->entries[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->entries[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->entries[$offset]);
    }
}
