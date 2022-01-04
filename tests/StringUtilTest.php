<?php

namespace tests\Dazet\TypeUtil;

use Dazet\TypeUtil\InvalidTypeException;
use Dazet\TypeUtil\StringUtil;
use PHPUnit\Framework\TestCase;
use stdClass;
use function array_filter;
use function tmpfile;

/** @covers StringUtil */
class StringUtilTest extends TestCase
{
    /** @dataProvider possibleStrings */
    public function test_string_like_value_canBeString($stringLike, $expectedString)
    {
        self::assertTrue(StringUtil::canBeString($stringLike));
        self::assertEquals($expectedString, StringUtil::toString($stringLike));
        self::assertEquals($expectedString, StringUtil::toStringOrNull($stringLike));
    }

    /** @dataProvider invalidString */
    public function test_invalid_value_canBeString_not($invalidString)
    {
        self::assertFalse(StringUtil::canBeString($invalidString));
        self::assertNull(StringUtil::toStringOrNull($invalidString));
    }

    /** @dataProvider invalidString */
    public function test_invalid_value_toString_throws_InvalidTypeException($invalidString)
    {
        $this->expectException(InvalidTypeException::class);
        StringUtil::toString($invalidString);
    }

    public function test_filter_possible_strings()
    {
        $stringObject = new StringObject('hello');

        self::assertEquals(
            ['hello', $stringObject, 123, true, null],
            array_filter(['hello', $stringObject, 123, true, null, ['array'], new stdClass()], StringUtil::canBeString)
        );
    }

    public function test_map_possible_strings()
    {
        $stringObject = new StringObject('hello');

        self::assertEquals(
            ['hello', 'hello', '123', '1', null, null, null],
            array_map(StringUtil::toStringOrNull, ['hello', $stringObject, 123, true, null, ['array'], new stdClass()])
        );
    }

    public function possibleStrings(): array
    {
        return [
            ['string', 'string'],
            [true, '1'],
            [false, '0'],
            [null, ''],
            [123, '123'],
            [123.45, '123.45'],
            [new StringObject('hello'), 'hello'],
        ];
    }

    public function invalidString(): array
    {
        return [[[]], [new stdClass()], [tmpfile()], [fn() => 'a']];
    }
}
