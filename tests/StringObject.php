<?php declare(strict_types=1);

namespace tests\Dazet\TypeUtil;

final class StringObject
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->value;
    }
}
