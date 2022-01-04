<?php declare(strict_types=1);

namespace Dazet\TypeUtil;

use function is_string;
use function json_decode;
use function json_encode;

final class JsonUtil
{
    /** @var callable(mixed):?string */
    public const toJsonOrNull = [self::class, 'toJsonOrNull'];
    /** @var callable(mixed):?array<int|string,mixed> */
    public const toArrayOrNull = [self::class, 'toArrayOrNull'];

    /**
     * @param mixed $value
     */
    public static function toJsonOrNull($value): ?string
    {
        $json = json_encode($value, JSON_UNESCAPED_UNICODE);

        return $json !== false ? $json : null;
    }

    /**
     * @param mixed $value
     * @return array<int|string, mixed>|null
     */
    public static function toArrayOrNull($value): ?array
    {
        if (!is_string($value)) {
            return null;
        }

        $array = json_decode($value, true);

        return ArrayUtil::toArrayOrNull($array);
    }
}
