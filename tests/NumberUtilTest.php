<?php

namespace tests\Dazet\TypeUtil;

use Dazet\TypeUtil\InvalidTypeException;
use Dazet\TypeUtil\NumberUtil;
use PHPUnit\Framework\TestCase;
use stdClass;
use function array_filter;
use function array_map;

/** @covers NumberUtil */
class NumberUtilTest extends TestCase
{
    /** @dataProvider possibleNumbers */
    public function test_possible_number_canBeNumber($possibleNumber)
    {
        self::assertTrue(NumberUtil::canBeNumber($possibleNumber));
    }

    /** @dataProvider invalidNumbers */
    public function test_invalid_number_value_canBeNumber_not($invalidNumber)
    {
        self::assertFalse(NumberUtil::canBeNumber($invalidNumber));
    }

    /** @dataProvider invalidNumbers */
    public function test_invalid_number_null_casting($invalidNumber)
    {
        self::assertNull(NumberUtil::toIntOrNull($invalidNumber));
        self::assertNull(NumberUtil::toFloatOrNull($invalidNumber));
    }

    /** @dataProvider invalidNumbers */
    public function test_invalid_number_casting_toInt_throws_InvalidTypeException($invalidNumber)
    {
        $this->expectException(InvalidTypeException::class);
        self::assertNull(NumberUtil::toInt($invalidNumber));
    }

    /** @dataProvider invalidNumbers */
    public function test_invalid_number_casting_toFloat_throws_InvalidTypeException($invalidNumber)
    {
        $this->expectException(InvalidTypeException::class);
        self::assertNull(NumberUtil::toFloat($invalidNumber));
    }

    public function test_int_number()
    {
        self::assertSame(123, NumberUtil::toInt(123));
        self::assertSame(123, NumberUtil::toIntOrNull(123));
        self::assertSame(123.0, NumberUtil::toFloat(123));
        self::assertSame(123.0, NumberUtil::toFloatOrNull(123));
    }

    public function test_float_number()
    {
        self::assertSame(123, NumberUtil::toInt(123.99));
        self::assertSame(123, NumberUtil::toIntOrNull(123.99));
        self::assertSame(123.99, NumberUtil::toFloat(123.99));
        self::assertSame(123.99, NumberUtil::toFloatOrNull(123.99));
    }

    public function test_int_string()
    {
        self::assertSame(123, NumberUtil::toInt('123'));
        self::assertSame(123, NumberUtil::toIntOrNull('123'));
        self::assertSame(123.0, NumberUtil::toFloat('123'));
        self::assertSame(123.0, NumberUtil::toFloatOrNull('123'));
    }

    public function test_float_string()
    {
        self::assertSame(123, NumberUtil::toInt('123.99'));
        self::assertSame(123, NumberUtil::toIntOrNull('123.99'));
        self::assertSame(123.99, NumberUtil::toFloat('123.99'));
        self::assertSame(123.99, NumberUtil::toFloatOrNull('123.99'));
    }

    public function test_bool_to_int()
    {
        self::assertSame(1, NumberUtil::toInt(true));
        self::assertSame(1, NumberUtil::toIntOrNull(true));
        self::assertSame(0, NumberUtil::toInt(false));
        self::assertSame(0, NumberUtil::toIntOrNull(false));
    }

    public function test_bool_to_float()
    {
        self::assertSame(1.0, NumberUtil::toFloat(true));
        self::assertSame(1.0, NumberUtil::toFloatOrNull(true));
        self::assertSame(0.0, NumberUtil::toFloat(false));
        self::assertSame(0.0, NumberUtil::toFloatOrNull(false));
    }

    public function test_number_filtering()
    {
        $numberObject = new StringObject('123.45');

        self::assertEquals(
            [1, 12, '123.99', $numberObject, null],
            array_filter([1, 12, '123.99', $numberObject, null, 'string', [], new stdClass()], NumberUtil::canBeNumber)
        );
    }

    public function test_number_map_to_int()
    {
        $numberObject = new StringObject('123.45');

        self::assertEquals(
            [1, 12, 123, 123, null, null],
            array_map(NumberUtil::toIntOrNull, [1, 12, '123.99', $numberObject, 'string', []])
        );

        self::assertEquals(
            [1.0, 12.0, 123.99, 123.45, null, null],
            array_map(NumberUtil::toFloatOrNull, [1, 12, '123.99', $numberObject, 'string', []])
        );
    }

    public function possibleNumbers(): array
    {
        return [
            [123],
            [123.45],
            ['123'],
            ['123.45'],
            [new StringObject('123.45')],
            [true],
            [false],
            [null],
        ];
    }

    public function invalidNumbers(): array
    {
        return [
            [[]],
            ['10 123.45'],
            [new StringObject('nine')],
            [new stdClass()],
        ];
    }
}
