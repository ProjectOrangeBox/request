<?php

declare(strict_types=1);

namespace orange\request\attributes\validations;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Validates that input contains a natural number greater than zero.
 */
class IsNaturalNoZero extends RequestAttribute
{
    protected string $errorMsg = '%s must contain a natural number greater than zero';

    /**
     * Checks whether the input is a natural number greater than zero.
     */
    public function validate(mixed $input): bool
    {
        $bool = false;

        if (is_scalar($input)) {
            $bool = preg_match('/^[1-9][0-9]*$/', (string)$input) === 1;
        }

        return $bool;
    }
}
