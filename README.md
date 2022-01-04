# Safe type casting utils for PHP

[![Build Status](https://travis-ci.org/dazet/type-utils.svg?branch=main)](https://travis-ci.org/dazet/type-utils)

## StringUtil

#### `StringUtil::canBeString(mixed $value): bool`

Returns `true` if `$value` can be safely casted to string: is scalar or object with `__toString` method or `null`.

`StringUtil::canBeString` method shortcut:
```php
$array = ['string', 123, [], null];
array_filter($array, StringUtil::canBeString); // ['string', 123, null]
```

#### `StringUtil::toStringOrNull(mixed $value): ?string`

Casts value to `string` if possible or returns `null`.

`StringUtil::toStringOrNull` method shortcut:
```php
$a = ['string', 123, [], new stdClass()];
array_map(StringUtil::toStringOrNull, $a); // ['string', '123', null, null]
```

#### `StringUtil::toString(mixed $value): string`

Casts value to `string` if possible or throws `InvalidTypeException`.
 
`StringUtil::toString` method shortcut:
```php
$a = ['string', 123, null];
array_map(StringUtil::toString, $a); // ['string', '123', '']

$b = ['string', new stdClass()];
array_map(StringUtil::toString, $b); // throws InvalidTypeException
```

## NumberUtil

#### `NumberUtil::canBeNumber(mixed $value): bool`

Returns true if value can be safely casted to `int|float`, which means that:
* it is number
* or numeric string 
* or can be casted to numeric string
* or is a boolean that can be transformed to `0|1`
* or is `null`

`NumberUtil::canBeNumber` method shortcut:
```php
$array = [123, 1.23, '123', 'string', null];
array_filter($array, NumberUtil::canBeNumber); // [123, 1.23, '123', null]
```

#### `NumberUtil::toIntOrNull(mixed $value): ?int`

Casts value to `int` if possible or returns `null`.

`NumberUtil::toIntOrNull` method shortcut:
```php
$a = [123, 1.23, 'string'];
array_map(NumberUtil::toIntOrNull, $a); // [123, 1, null]
```

#### `NumberUtil::toInt(mixed $value): int`

Casts value to `int` if possible or throws `InvalidTypeException`.

#### `NumberUtil::toFloatOrNull(mixed $value): ?float`

Casts value to `float` if possible or returns `null`.

`NumberUtil::toFloatOrNull` method shortcut:
```php
$a = [123, 1.23, 'string'];
array_map(NumberUtil::toFloatOrNull, $a); // [123.0, 1.23, null]
```

#### `NumberUtil::toFloat(mixed $value): float`

Casts value to `float` if possible or throws `InvalidTypeException`.

## ArrayUtil

#### `ArrayUtil::canBeArray(mixed $value): bool`

Returns true if `$value` is or can be converted to `array`, which means it is iterable or `null`.

`ArrayUtil::canBeArray` method shortcut:
```php
$array = [['array'], new ArrayObject(['object']), 'string'];
array_filter($array, ArrayUtil::canBeArray); // [['array'], new ArrayObject(['object'])]
```

#### `ArrayUtil::toArrayOrNull(mixed $value): ?array<int|string, mixed>`

Transforms `$value` to `array` (keeping keys) or returns `null`.

`ArrayUtil::toArrayOrNull` method shortcut:
```php
$a = ['a' => ['array'], 'b' => 'string', 'c' => new ArrayObject(['object'])];
array_map(ArrayUtil::toArrayOrNull, $a); // ['a' => ['array'], 'b' => null, 'c' => ['object']]

$iterate = function() {
    yield 9 => 'nine';
    yield 9 => 'nine';
    yield 9 => 'nine';
};
array_map(ArrayUtil::toArrayOrNull, $iterate()); // [9 => 'nine']
```

#### `ArrayUtil::toArrayListOrNull(mixed $value): ?array<int, mixed>`

Transforms `$value` to `array-list` (reindex keys) or returns `null`.

`ArrayUtil::toArrayListOrNull` method shortcut:
```php
$a = ['a' => ['array'], 'b' => 'string', 'c' => new ArrayObject(['object'])];
array_map(ArrayUtil::toArrayListOrNull, $a); // [['array'], null, ['object']]

$iterate = function() {
    yield 9 => 'nine';
    yield 9 => 'nine';
    yield 9 => 'nine';
};
array_map(ArrayUtil::toArrayOrNull, $iterate()); // ['nine', 'nine', 'nine']
```

#### `ArrayUtil::toArray(mixed $value): array<int|string, mixed>`

Transforms `$value` to `array` (keeping keys) or throws `InvalidTypeException`.

#### `ArrayUtil::toArrayList(mixed $value): array<int, mixed>`

Transforms `$value` to `array-list` (reindex keys) or throws `InvalidTypeException`.

#### `ArrayUtil::isCountable(mixed $value): bool`

Same as standard `is_countable`, additionally it allows to shortcut callable by `ArrayUtil::isCountable`.

#### `ArrayUtil::countOrNull(mixed $value): ?int`

Returns count if `$value` is countable or returns `null`.

## BooleanUtil

#### `BooleanUtil::canBeBool(mixed $value): bool`

Returns true if `$value` can be taken as boolean (true, false, 0, 1).

`BooleanUtil::canBeBool` shortcut also available.

#### `BooleanUtil::toBoolOrNull(mixed $value): ?bool`

Casts value to `bool` if possible or returns `null`.

#### `BooleanUtil::toBool(mixed $value): ?bool`

Casts value to `bool` if possible or throws `InvalidTypeException`.

## DateUtil

#### `DateUtil::canBeDate(mixed $value): bool`

Returns `true` if `$value` can be converted to `DateTimeImmutable`, 
which means it has `DateTimeInterface` or is a string supported by `strtotime`.

`DateUtil::canBeDate` method shortcut:
```php
$now = new DateTime('now');
$a = ['today', $now, 'never'];
array_filter($a, DateUtil::canBeDate); // ['today', $now]
```

#### `DateUtil::toDatetimeOrNull(mixed $value): ?DateTimeImmutable`

Transforms value to `DateTimeImmutable` if possible or returns `null`.

Shortcut `DateUtil::toDatetimeOrNull` also available.

#### `DateUtil::toDatetime(mixed $value): DateTimeImmutable`

Transforms value to `DateTimeImmutable` if possible or throws `InvalidTypeException`.

Shortcut `DateUtil::toDatetime` also available.

#### `DateUtil::toDateFormatOrNull(mixed $value, string $format): ?string`

Transforms `$value` to given date format if `$value` can be date or returns `null` otherwise.

#### `DateUtil::toDateFormat(mixed $value, string $format): string`

Transforms `$value` to given date format if `$value` can be date or throws `InvalidTypeException`.

#### `DateUtil::toTimestampOrNull(mixed $value): ?int`

Transforms `$value` to Unix timestamp if `$value` can be date or returns `null` otherwise.

#### `DateUtil::toTimestamp(mixed $value): int`

Transforms `$value` to Unix timestamp if `$value` can be date or throws `InvalidTypeException`.
