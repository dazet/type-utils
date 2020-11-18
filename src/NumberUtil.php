<?php declare(strict_types=1);

namespace Dazet\TypeUtil;

use function is_bool;
use function is_float;
use function is_int;
use function is_numeric;

final class NumberUtil
{
    /** @var callable(mixed):bool */
    public const canBeNumber = [self::class, 'canBeNumber'];
    /** @var callable(mixed):?int */
    public const toIntOrNull = [self::class, 'toIntOrNull'];
    /** @var callable(mixed):int */
    public const toInt = [self::class, 'toInt'];
    /** @var callable(mixed):?float */
    public const toFloatOrNull = [self::class, 'toFloatOrNull'];
    /** @var callable(mixed):float */
    public const toFloat = [self::class, 'toFloat'];

    /**
     * @param mixed $value
     */
    public static function canBeNumber($value): bool
    {
        return is_numeric($value) || is_bool($value)
            || (StringUtil::canBeString($value) && is_numeric(StringUtil::toString($value)));
    }

    /**
     * @param mixed $value
     */
    public static function toIntOrNull($value): ?int
    {
        if ($value === null || !self::canBeNumber($value)) {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_float($value)) {
            return (int)$value;
        }

        if (is_bool($value)) {
            return $value ? 1 : 0;
        }

        return (int)(string)$value;
    }

    /**
     * @param mixed $value
     * @throws InvalidTypeException
     */
    public static function toInt($value): int
    {
        $value = self::toIntOrNull($value);

        if ($value === null) {
            throw InvalidTypeException::of($value, 'int');
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    public static function toFloatOrNull($value): ?float
    {
        if ($value === null || !self::canBeNumber($value)) {
            return null;
        }

        if (is_float($value)) {
            return $value;
        }

        return (float)(string)$value;
    }

    /**
     * @param mixed $value
     * @throws InvalidTypeException
     */
    public static function toFloat($value): float
    {
        $value = self::toFloatOrNull($value);

        if ($value === null) {
            throw InvalidTypeException::of($value, 'float');
        }

        return $value;
    }
}
