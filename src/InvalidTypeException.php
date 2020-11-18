<?php declare(strict_types=1);

namespace Dazet\TypeUtil;

use InvalidArgumentException;
use function gettype;

final class InvalidTypeException extends InvalidArgumentException
{
    /**
     * @param mixed $value
     */
    public static function of($value, string $cast): self
    {
        $type = gettype($value);

        return new self("Given value of type {$type} cannot be casted to {$cast}");
    }
}
