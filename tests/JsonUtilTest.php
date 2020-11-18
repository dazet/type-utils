<?php

namespace tests\Dazet\TypeUtil;

use Dazet\TypeUtil\JsonUtil;
use PHPUnit\Framework\TestCase;
use function json_encode;
use function tmpfile;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_UNICODE;

/** @covers JsonUtil */
class JsonUtilTest extends TestCase
{
    /** @dataProvider possibleJsons */
    public function test_to_json($value)
    {
        self::assertEquals(
            json_encode($value, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE),
            JsonUtil::toJsonOrNull($value)
        );
    }

    public function test_returns_null_when_cannot_encode_json()
    {
        $file = tmpfile();
        self::assertNull(JsonUtil::toJsonOrNull($file));
    }

    public function test_decoding_json()
    {
        self::assertEquals(['message' => 'hello world'], JsonUtil::toArrayOrNull('{"message":"hello world"}'));
    }

    public function test_returns_null_when_cannot_decode_json()
    {
        self::assertNull(JsonUtil::toArrayOrNull('{"message":"hello world"'));
    }

    public function test_returns_null_when_not_a_string()
    {
        self::assertNull(JsonUtil::toArrayOrNull([]));
    }

    public function possibleJsons(): array
    {
        return [
            [['message' => 'hello world']],
            ['hello world'],
            [null],
            [123],
        ];
    }
}
