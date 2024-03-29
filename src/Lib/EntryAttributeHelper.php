<?php

declare(strict_types=1);

namespace Gubler\ADSearchBundle\Lib;

use Symfony\Component\Ldap\Entry;

final class EntryAttributeHelper
{
    public const RETURN_FIRST_VALUE = 0;
    public const RETURN_ALL_VALUES = 1;

    public static function getAttribute(
        Entry $entry,
        string $attribute,
        bool $caseSensitive = false,
        int $return = self::RETURN_FIRST_VALUE,
    ): string {
        $attributeValue = $entry->getAttribute(name: $attribute, caseSensitive: $caseSensitive);

        if (!\is_array(value: $attributeValue)) {
            throw new \BadMethodCallException(message: 'Requested attribute does not exist.');
        }

        return match ($return) {
            self::RETURN_FIRST_VALUE => $attributeValue[0],
            self::RETURN_ALL_VALUES => $attributeValue,
            default => throw new \InvalidArgumentException(message: 'Invalid return value'),
        };
    }

    public static function getAttributeOrNull(
        Entry $entry,
        string $attribute,
        bool $caseSensitive = false,
        int $return = self::RETURN_FIRST_VALUE,
    ): ?string {
        $attributeValue = $entry->getAttribute(name: $attribute, caseSensitive: $caseSensitive);

        if (!\is_array(value: $attributeValue)) {
            return null;
        }

        return match ($return) {
            self::RETURN_FIRST_VALUE => $attributeValue[0],
            self::RETURN_ALL_VALUES => $attributeValue,
            default => throw new \InvalidArgumentException(message: 'Invalid return value'),
        };
    }
}
