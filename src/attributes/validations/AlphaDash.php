<?php

declare(strict_types=1);

namespace orange\request\attributes\validations;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Validates that input contains only alpha and dash characters.
 */
class AlphaDash extends RequestAttribute
{
    protected string $errorMsg = '%s may only contain alpha-numeric characters, underscores, and dashes';

    /**
     * Checks whether the input matches the class alpha-dash rule.
     */
    public function validate(mixed $input): bool
    {
        $bool = false;

        if (is_string($input)) {
            $bool = preg_match('/^[a-zA-Z-]+$/', $input) === 1;
        }

        return $bool;
    }
}
