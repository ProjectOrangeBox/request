<?php

declare(strict_types=1);

namespace orange\request\attributes\validations;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Validates that input contains a valid base64 string.
 */
class ValidBase64 extends RequestAttribute
{
    protected string $errorMsg = '%s must contain a valid base64 string';

    /**
     * Checks whether the input can be decoded and re-encoded as base64.
     */
    public function validate(mixed $input): bool
    {
        $bool = false;

        if (is_string($input)) {
            $decoded = base64_decode($input, true);

            $bool = $decoded !== false && base64_encode($decoded) === $input;
        }

        return $bool;
    }
}
