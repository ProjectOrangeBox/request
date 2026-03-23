<?php

declare(strict_types=1);

namespace orange\request\attributes;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Label extends RequestAttribute
{
    public function __construct(protected string $name = '') {}

    public function getName(): string
    {
        return $this->name;
    }
}
