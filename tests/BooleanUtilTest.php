<?php

namespace tests\Dazet\TypeUtil;

use Dazet\TypeUtil\BooleanUtil;
use Dazet\TypeUtil\InvalidTypeException;
use PHPUnit\Framework\TestCase;
use stdClass;
use function array_filter;
use function array_map;

/** @covers BooleanUtil */
class BooleanUtilTest extends TestCase
{
    /** @dataProvider booleanLikes */
    public function test_like_bool_canBeBool($bool)
    {
        self::assertTrue(BooleanUtil::canBeBool($bool));
    }

    /** @dataProvider booleanNotLikes */
    public function test_not_like_bool_canBeBool_not($notLikeBool)
    {
        self::assertFalse(BooleanUtil::canBeBool($notLikeBool));
    }

    /** @dataProvider truthLikes */
    public function test_truth_is_true($truth)
    {
        self::assertTrue(BooleanUtil::toBool($truth));
        self::assertTrue(BooleanUtil::toBoolOrNull($truth));
    }

    /** @dataProvider fallacyLikes */
    public function test_fallacy_is_false($fallacy)
    {
        self::assertFalse(BooleanUtil::toBool($fallacy));
        self::assertFalse(BooleanUtil::toBoolOrNull($fallacy));
    }

    /** @dataProvider booleanNotLikes */
    public function test_not_like_bool_toBoolOrNull_is_null($notLikeBool)
    {
        self::assertNull(BooleanUtil::toBoolOrNull($notLikeBool));
    }

    /** @dataProvider booleanNotLikes */
    public function test_throwing_InvalidTypeException_when_not_like_bool_casted_toBool($notLikeBool)
    {
        $this->expectException(InvalidTypeException::class);
        BooleanUtil::toBool($notLikeBool);
    }

    public function test_filter_boolean_likes()
    {
        self::assertEquals(
            [true, 2 => false, 3 => 0, 4 => 1],
            array_filter([true, 'string', false, 0, 1, [], 123.3], BooleanUtil::canBeBool)
        );
    }

    public function test_map_boolean_likes()
    {
        self::assertEquals(
            [true, null, false, false, true, null, null],
            array_map(BooleanUtil::toBoolOrNull, [true, 'string', false, 0, 1, [], 123.3])
        );

        self::assertEquals(
            [true, false, false, true],
            array_map(BooleanUtil::toBool, [true, false, 0, 1])
        );
    }

    public function booleanLikes(): array
    {
        return [...$this->truthLikes(), ...$this->fallacyLikes()];
    }

    public function truthLikes(): array
    {
        return [[true], [1], ['1']];
    }

    public function fallacyLikes(): array
    {
        return [[false], [0], ['0']];
    }

    public function booleanNotLikes(): array
    {
        return [['true'], ['false'], [0.0], [''], [null], [new stdClass()], [[]],];
    }
}
