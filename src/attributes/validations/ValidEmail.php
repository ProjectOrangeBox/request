<?php

declare(strict_types=1);

namespace orange\request\attributes\validations;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Validates that input contains a single valid email address.
 */
class ValidEmail extends RequestAttribute
{
    protected string $errorMsg = '%s must contain a valid email address';

    /**
     * Checks whether the input is a valid email address.
     */
    public function validate(mixed $input): bool
    {
        $bool = false;

        if (is_string($input)) {
            $bool = filter_var($input, FILTER_VALIDATE_EMAIL) !== false;
        }

        return $bool;
    }
}
