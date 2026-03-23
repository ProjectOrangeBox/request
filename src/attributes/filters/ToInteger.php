<?php

declare(strict_types=1);

namespace orange\request\attributes\filters;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class ToInteger extends RequestAttribute
{
    public function __construct() {}

    public function filter(mixed $input): mixed
    {
        return (int)$input;
    }
}
