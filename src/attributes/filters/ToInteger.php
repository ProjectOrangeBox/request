<?php

declare(strict_types=1);

namespace orange\request\attributes\filters;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Casts request input to an integer.
 */
class ToInteger extends RequestAttribute
{
    /**
     * Returns the integer-cast value.
     */
    public function filter(mixed $input): mixed
    {
        return (int)$input;
    }
}
