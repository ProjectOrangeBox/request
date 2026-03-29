<?php

declare(strict_types=1);

namespace orange\request\attributes\filters;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Casts request input to a string.
 */
class ToString extends RequestAttribute
{
    /**
     * Returns the string-cast value.
     */
    public function filter(mixed $input): mixed
    {
        return (string)$input;
    }
}
