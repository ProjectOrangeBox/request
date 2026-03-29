<?php

declare(strict_types=1);

namespace orange\request\attributes\filters;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Truncates string input to a maximum length.
 */
class StrLimit extends RequestAttribute
{
    /**
     * Stores the maximum string length allowed by the filter.
     */
    public function __construct(private int $length) {}

    /**
     * Returns the original value or the truncated string result.
     */
    public function filter(mixed $input): mixed
    {
        // Default to returning the original value unchanged.
        $output = $input;

        // Only limit the value when the input is a string.
        if (is_string($input)) {
            // Trim the string to the configured maximum length.
            $output = substr($input, 0, $this->length);
        }

        // Return the original value or the shortened string.
        return $output;
    }
}
