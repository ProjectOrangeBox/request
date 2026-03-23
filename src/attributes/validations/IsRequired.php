<?php

declare(strict_types=1);

namespace orange\request\attributes\validations;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class IsRequired extends RequestAttribute
{
    public function __construct(private string $message = 'This field is required') {}

    public function validate(mixed $input): bool
    {
        return !empty($input);
    }

    public function getMessage(string $human): string
    {
        $errorMsg = $this->message ?: $human . ' is required';

        return sprintf($errorMsg, $human);
    }
}
