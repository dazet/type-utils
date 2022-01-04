<?php declare(strict_types=1);

namespace Dazet\TypeUtil;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use function ctype_digit;
use function is_int;
use function is_string;
use function strtotime;

final class DateUtil
{
    /** @var callable(mixed $value):bool */
    public const canBeDate = [self::class, 'canBeDate'];
    /** @var callable(mixed $value, ?DateTimeZone $timeZone = null):?DateTimeImmutable */
    public const toDatetimeOrNull = [self::class, 'toDatetimeOrNull'];
    /** @var callable(mixed $value, ?DateTimeZone $timeZone = null):DateTimeImmutable */
    public const toDatetime = [self::class, 'toDatetime'];
    /** @var callable(mixed $value, string $format):?string */
    public const toDateFormatOrNull = [self::class, 'toDateFormatOrNull'];
    /** @var callable(mixed $value, string $format):string */
    public const toDateFormat = [self::class, 'toDateFormat'];
    /** @var callable(mixed $value):?int */
    public const toTimestampOrNull = [self::class, 'toTimestampOrNull'];
    /** @var callable(mixed $value, string $modifier):?DateTimeImmutable */
    public const dateModifyOrNull = [self::class, 'dateModifyOrNull'];

    /**
     * @param mixed $value
     */
    public static function canBeDate($value): bool
    {
        return $value instanceof DateTimeInterface
            || (StringUtil::canBeString($value) && strtotime(StringUtil::toString($value)) !== false);
    }

    /**
     * @param mixed $value
     */
    public static function toDatetimeOrNull($value, ?DateTimeZone $timeZone = null): ?DateTimeImmutable
    {
        if ($value instanceof DateTime) {
            return DateTimeImmutable::createFromMutable($value);
        }

        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        $stringValue = StringUtil::toStringOrNull($value);
        if ($stringValue !== null && strtotime($stringValue) !== false) {
            return new DateTimeImmutable($stringValue, $timeZone);
        }

        return null;
    }

    /**
     * @param mixed $value
     * @throws InvalidTypeException
     */
    public static function toDatetime($value, ?DateTimeZone $timeZone = null): DateTimeImmutable
    {
        $datetime = self::toDatetimeOrNull($value, $timeZone);

        if ($datetime === null) {
            throw InvalidTypeException::of($value, 'DateTimeImmutable');
        }

        return $datetime;
    }

    /**
     * @param mixed $value
     */
    public static function toDateFormatOrNull($value, string $format): ?string
    {
        $datetime = self::toDatetimeOrNull($value);

        if ($datetime === null) {
            return null;
        }

        return $datetime->format($format);
    }

    /**
     * @param mixed $value
     * @throws InvalidTypeException
     */
    public static function toDateFormat($value, string $format): string
    {
        return self::toDatetime($value)->format($format);
    }

    /**
     * @param mixed $value
     */
    public static function toTimestampOrNull($value): ?int
    {
        if (is_int($value)) {
            return $value;
        }

        if (is_string($value) && ctype_digit($value)) {
            return NumberUtil::toInt($value);
        }

        if (self::canBeDate($value)) {
            return self::toDatetime($value)->getTimestamp();
        }

        return null;
    }

    /**
     * @param mixed $value
     */
    public static function dateModifyOrNull($value, string $modifier): ?DateTimeImmutable
    {
        $datetime = self::toDatetimeOrNull($value);

        if ($datetime === null) {
            return null;
        }

        return $datetime->modify($modifier);
    }
}
