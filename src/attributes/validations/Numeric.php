<?php

declare(strict_types=1);

namespace orange\request\attributes\validations;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Validates that input contains a numeric value.
 */
class Numeric extends RequestAttribute
{
    protected string $errorMsg = '%s must contain only numbers';

    /**
     * Checks whether the input is numeric.
     */
    public function validate(mixed $input): bool
    {
        return is_scalar($input) && is_numeric((string)$input);
    }
}
