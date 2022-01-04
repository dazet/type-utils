<?php declare(strict_types=1);

namespace Dazet\TypeUtil;

use Traversable;
use function array_values;
use function count;
use function is_array;
use function is_countable;
use function is_iterable;
use function iterator_to_array;

final class ArrayUtil
{
    /** @var callable(mixed):bool */
    public const canBeArray = [self::class, 'canBeArray'];
    /** @var callable(mixed):?array<int|string, mixed> */
    public const toArrayOrNull = [self::class, 'toArrayOrNull'];
    /** @var callable(mixed):array<int|string, mixed> */
    public const toArray = [self::class, 'toArray'];
    /** @var callable(mixed):?array<int, mixed> */
    public const toArrayListOrNull = [self::class, 'toArrayListOrNull'];
    /** @var callable(mixed):array<int, mixed> */
    public const toArrayList = [self::class, 'toArrayList'];
    /** @var callable(mixed):bool */
    public const isCountable = [self::class, 'isCountable'];
    /** @var callable(mixed):?int */
    public const countOrNull = [self::class, 'countOrNull'];

    /** @param mixed $value */
    public static function canBeArray($value): bool
    {
        return $value === null || is_iterable($value);
    }

    /**
     * @param mixed $value
     * @return array<int|string, mixed>|null
     */
    public static function toArrayOrNull($value): ?array
    {
        if (is_array($value)) {
            return $value;
        }

        if ($value instanceof Traversable) {
            return iterator_to_array($value);
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return array<int, mixed>|null
     */
    public static function toArrayListOrNull($value): ?array
    {
        if (is_array($value)) {
            return array_values($value);
        }

        if ($value instanceof Traversable) {
            return iterator_to_array($value, false);
        }

        return null;
    }

    /**
     * @param mixed $value
     * @return array<int|string, mixed>
     * @throws InvalidTypeException
     */
    public static function toArray($value): array
    {
        if ($value === null) {
            return [];
        }

        $value = self::toArrayOrNull($value);

        if ($value === null) {
            throw InvalidTypeException::of($value, 'array');
        }

        return $value;
    }

    /**
     * @param mixed $value
     * @return array<int, mixed>
     * @throws InvalidTypeException
     */
    public static function toArrayList($value): array
    {
        if ($value === null) {
            return [];
        }

        $value = self::toArrayListOrNull($value);

        if ($value === null) {
            throw InvalidTypeException::of($value, 'array list');
        }

        return $value;
    }

    /** @param mixed $value */
    public static function isCountable($value): bool
    {
        return is_countable($value);
    }

    /** @param mixed $value */
    public static function countOrNull($value): ?int
    {
        return is_countable($value) ? count($value) : null;
    }
}
