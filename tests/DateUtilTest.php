<?php

namespace tests\Dazet\TypeUtil;

use DateTime;
use DateTimeImmutable;
use Dazet\TypeUtil\DateUtil;
use Dazet\TypeUtil\InvalidTypeException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function array_filter;
use function array_map;

/** @covers DateUtil */
class DateUtilTest extends TestCase
{
    /** @dataProvider possibleDates */
    public function test_possible_date_canBeDate($possibleDatetime)
    {
        self::assertTrue(DateUtil::canBeDate($possibleDatetime));
        self::assertNotNull(DateUtil::toDatetimeOrNull($possibleDatetime));
        DateUtil::toDatetime($possibleDatetime);
    }

    public function test_DateTime_toDatetime()
    {
        $date = new DateTime('now');
        $expected = DateTimeImmutable::createFromMutable($date);
        self::assertEquals($expected, DateUtil::toDatetime($date));
        self::assertEquals($expected, DateUtil::toDatetimeOrNull($date));
    }

    public function test_DateTimeImmutable_toDatetime()
    {
        $date = new DateTimeImmutable('now');
        self::assertEquals($date, DateUtil::toDatetime($date));
        self::assertEquals($date, DateUtil::toDatetimeOrNull($date));
    }

    /** @dataProvider validTimeStrings */
    public function test_string_toDatetime(string $timeString)
    {
        $expected = new DateTimeImmutable($timeString);
        self::assertEquals($expected, DateUtil::toDatetime($timeString));
        self::assertEquals($expected, DateUtil::toDatetimeOrNull($timeString));
    }

    /** @dataProvider invalidDates */
    public function test_invalid_dates_toDatetimeOrNull_gives_null($invalidDate)
    {
        self::assertNull(DateUtil::toDatetimeOrNull($invalidDate));
    }

    /** @dataProvider invalidDates */
    public function test_invalid_dates_toDatetime_throws_InvalidTypeException($invalidDate)
    {
        $this->expectException(InvalidTypeException::class);
        DateUtil::toDatetime($invalidDate);
    }

    /** @dataProvider invalidDates */
    public function test_invalid_dates_toDateFormatOrNull_gives_null($invalidDate)
    {
        self::assertNull(DateUtil::toDateFormatOrNull($invalidDate, 'Y-m-d'));
    }

    /** @dataProvider invalidDates */
    public function test_invalid_dates_toDateFormatOrNull_throws_InvalidTypeException($invalidDate)
    {
        $this->expectException(InvalidTypeException::class);
        DateUtil::toDateFormat($invalidDate, 'Y-m-d');
    }

    public function test_DateTime_toDateFormat()
    {
        $date = new DateTime('now');
        $format = 'Y-m-d';
        $expected = DateTimeImmutable::createFromMutable($date)->format($format);
        self::assertEquals($expected, DateUtil::toDateFormat($date, $format));
        self::assertEquals($expected, DateUtil::toDateFormatOrNull($date, $format));
    }

    public function test_DateTimeImmutable_toDateFormat()
    {
        $date = new DateTimeImmutable('now');
        $format = 'Y-m-d';
        $expected = $date->format($format);
        self::assertEquals($expected, DateUtil::toDateFormat($date, $format));
        self::assertEquals($expected, DateUtil::toDateFormatOrNull($date, $format));
    }

    /** @dataProvider validTimeStrings */
    public function test_string_toDateFormat(string $timeString)
    {
        $format = 'Y-m-d';
        $expected = (new DateTimeImmutable($timeString))->format($format);
        self::assertEquals($expected, DateUtil::toDateFormat($timeString, $format));
        self::assertEquals($expected, DateUtil::toDateFormatOrNull($timeString, $format));
    }

    /** @dataProvider possibleDates */
    public function test_possible_dates_filter($possibleDatetime)
    {
        self::assertEquals(
            [1 => $possibleDatetime],
            array_filter(['hello', $possibleDatetime, 123, null, false], DateUtil::canBeDate)
        );
    }

    /** @dataProvider possibleDates */
    public function test_possible_dates_map($possibleDatetime)
    {
        self::assertEquals(
            [null, DateUtil::toDatetime($possibleDatetime), null, null, null],
            array_map(DateUtil::toDatetimeOrNull, ['hello', $possibleDatetime, 123, null, false])
        );
    }

    /** @dataProvider possibleDates */
    public function test_possible_dates_to_timestamp($possibleDatetime)
    {
        $date = DateUtil::toDatetime($possibleDatetime);
        self::assertEquals($date->getTimestamp(), DateUtil::toTimestampOrNull($possibleDatetime));
    }

    public function test_invalid_dates_toTimestampOrNull_gives_null()
    {
        self::assertNull(DateUtil::toTimestampOrNull('not a date'));
    }

    /** @dataProvider possibleDates */
    public function test_possible_dates_dateModifyOrNull($possibleDatetime)
    {
        $date = DateUtil::toDatetime($possibleDatetime);
        $modify = '+3 hours';
        self::assertEquals($date->modify($modify), DateUtil::dateModifyOrNull($possibleDatetime, $modify));
    }

    public function test_invalid_dates_dateModifyOrNull_gives_null()
    {
        self::assertNull(DateUtil::dateModifyOrNull('not a date', '+1 day'));
    }

    public function possibleDates(): array
    {
        return [
            [new DateTime('now')],
            [new DateTimeImmutable('now')],
            ['2020-11-18 16:00:00'],
            ['today'],
            [new StringObject('2020-11-18 16:00:00')],
        ];
    }

    public function validTimeStrings(): array
    {
        return [
            ['2020-11-18 16:00:00'],
            ['today'],
            ['16:00'],
        ];
    }

    public function invalidDates(): array
    {
        return [
            ['birthday'],
            ['the end'],
            [[]],
            [123],
            [123.45],
            [''],
            [null],
            [new stdClass()],
        ];
    }
}
