<?php

namespace tests\Dazet\TypeUtil;

use ArrayIterator;
use ArrayObject;
use Countable;
use DateTimeImmutable;
use Dazet\TypeUtil\ArrayUtil;
use Dazet\TypeUtil\InvalidTypeException;
use IteratorAggregate;
use PHPUnit\Framework\TestCase;
use stdClass;
use Traversable;
use function array_filter;
use function array_map;

/** @covers ArrayUtil */
class ArrayUtilTest extends TestCase
{
    /** @dataProvider iterableLists */
    public function test_possible_array_casts($arrayLike, array $expectedArray)
    {
        self::assertTrue(ArrayUtil::canBeArray($arrayLike));
        self::assertEquals($expectedArray, ArrayUtil::toArrayOrNull($arrayLike));
        self::assertEquals($expectedArray, ArrayUtil::toArray($arrayLike));
    }

    public function test_casting_null_to_array()
    {
        self::assertTrue(ArrayUtil::canBeArray(null));
        self::assertEquals([], ArrayUtil::toArray(null));
        self::assertEquals(null, ArrayUtil::toArrayOrNull(null));
    }

    /** @dataProvider nonIterables */
    public function test_toArrayOrNull_cast_to_null_impossible_array_types($arrayNotLike)
    {
        self::assertFalse(ArrayUtil::canBeArray($arrayNotLike));
        self::assertNull(ArrayUtil::toArrayOrNull($arrayNotLike));
    }

    /** @dataProvider nonIterables */
    public function test_toArray_throws_InvalidTypeException_on_impossible_array_types($arrayNotLike)
    {
        $this->expectException(InvalidTypeException::class);
        ArrayUtil::toArray($arrayNotLike);
    }

    public function test_filtering_iterables_in_array()
    {
        self::assertEquals(
            [['array one'], new ArrayObject(['array two'])],
            array_filter([['array one'], new ArrayObject(['array two']), 'not array'], ArrayUtil::canBeArray)
        );
    }

    public function test_mapping_iterables_in_array()
    {
        self::assertEquals(
            [['array one'], ['array two']],
            array_map(ArrayUtil::toArray, [['array one'], new ArrayObject(['array two'])])
        );

        self::assertEquals(
            [['array one'], ['array two'], null],
            array_map(ArrayUtil::toArrayOrNull, [['array one'], new ArrayObject(['array two']), 'not array'])
        );
    }

    public function test_throwing_InvalidTypeException_when_mapping_toArray_not_iterable_item()
    {
        $this->expectException(InvalidTypeException::class);
        array_map(ArrayUtil::toArray, [['array one'], 'not array']);
    }

    public function test_casting_to_array_of_lists()
    {
        self::assertEquals(
            [['array one'], ['array two']],
            array_map(ArrayUtil::toArrayList, [['key' => 'array one'], new ArrayObject(['key' => 'array two'])])
        );

        self::assertEquals(
            [['array one'], null],
            array_map(ArrayUtil::toArrayListOrNull, [['key' => 'array one'], 'not array'])
        );
    }

    public function test_countable_isCountable()
    {
        self::assertTrue(ArrayUtil::isCountable(['one']));

        $countable = new class implements Countable {
            public function count(): int
            {
                return 123;
            }
        };
        self::assertTrue(ArrayUtil::isCountable($countable));
    }

    public function test_not_countable_isCountable_not()
    {
        self::assertFalse(ArrayUtil::isCountable(new stdClass()));
        self::assertNull(ArrayUtil::countOrNull(new stdClass()));
        self::assertFalse(ArrayUtil::isCountable('string'));
        self::assertNull(ArrayUtil::countOrNull('string'));
        self::assertFalse(ArrayUtil::isCountable(123));
        self::assertNull(ArrayUtil::countOrNull(123));
    }

    public function test_filtering_countable_in_array()
    {
        $countable = new class implements Countable {
            public function count(): int
            {
                return 123;
            }
        };
        $array = [['array one'], new ArrayObject(['array two']), $countable, 'not array'];

        self::assertEquals(
            [['array one'], new ArrayObject(['array two']), $countable],
            array_filter($array, ArrayUtil::isCountable)
        );
    }

    public function test_mapping_list_of_countable()
    {
        $countable = new class implements Countable {
            public function count(): int
            {
                return 123;
            }
        };
        $array = [['array one'], new ArrayObject(['array two']), $countable, 'not array'];

        self::assertEquals(
            [1, 1, 123, null],
            array_map(ArrayUtil::countOrNull, $array)
        );
    }

    /** @return array<int, iterable<int, mixed>> */
    public function iterableLists(): array
    {
        return [
            [['a', 'b', 'c'], ['a', 'b', 'c']],
            [new ArrayObject(['a', 'b', 'c']), ['a', 'b', 'c']],
            [new ArrayIterator(['a', 'b', 'c']), ['a', 'b', 'c']],
            [
                new class implements IteratorAggregate {
                    public function getIterator(): Traversable
                    {
                        yield 'a';
                        yield 'b';
                        yield 'c';
                    }
                },
                ['a', 'b', 'c']
            ],
        ];
    }

    public function nonIterables(): array
    {
        return [
            ['string'],
            [123],
            [123.45],
            [new stdClass()],
            [new DateTimeImmutable()],
        ];
    }
}
