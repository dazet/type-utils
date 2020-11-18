<?php declare(strict_types=1);

namespace Dazet\TypeUtil;

use function is_bool;
use function is_object;
use function is_scalar;
use function method_exists;

final class StringUtil
{
    /** * @var callable(mixed):bool */
    public const canBeString = [self::class, 'canBeString'];
    /** @var callable(mixed):?string */
    public const toStringOrNull = [self::class, 'toStringOrNull'];
    /** @var callable(mixed):string */
    public const toString = [self::class, 'toString'];

    /**
     * @param mixed $value
     */
    public static function canBeString($value): bool
    {
        return is_scalar($value) || (is_object($value) && method_exists($value, '__toString'));
    }

    /**
     * @param mixed $value
     */
    public static function toStringOrNull($value): ?string
    {
        if ($value === null || !self::canBeString($value)) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        return (string)$value;
    }

    /**
     * @param mixed $value
     * @throws InvalidTypeException
     */
    public static function toString($value): string
    {
        $string = self::toStringOrNull($value);

        if ($string === null) {
            throw InvalidTypeException::of($value, 'string');
        }

        return $string;
    }
}
