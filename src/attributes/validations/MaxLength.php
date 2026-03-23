<?php

declare(strict_types=1);

namespace orange\request\attributes\validations;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class MaxLength extends RequestAttribute
{
    public function __construct(private int $length, private string $message = '') {}

    public function validate(mixed $input): bool
    {
        $bool = false;

        if (is_string($input)) {
            $bool = strlen($input) < $this->length;
        }

        return $bool;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getMessage(string $human): string
    {
        $errorMsg = $this->message ?: $human . ' must be less than ' . $this->length . ' characters';

        return sprintf($errorMsg, $human, $this->length);
    }
}
