<?php

declare(strict_types=1);

namespace orange\request\attributes\validations;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class LessThan extends RequestAttribute
{
    public function __construct(protected int $length, private string $message = '') {}

    public function validate(mixed $input): bool
    {
        $bool = false;

        if (is_int($input)) {
            $bool = $input < $this->length;
        }

        return $bool;
    }

    public function getValue(): int
    {
        return $this->length;
    }

    public function getMessage(string $human): string
    {
        $errorMsg = $this->message ?: $human . ' must be less than ' . $this->length;

        return sprintf($errorMsg, $human, $this->length);
    }
}
