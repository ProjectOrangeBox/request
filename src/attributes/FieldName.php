<?php

declare(strict_types=1);

namespace orange\request\attributes;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
/**
 * Declares the input field name for a request property.
 */
class FieldName extends RequestAttribute
{
    /**
     * Stores the configured field name.
     */
    public function __construct(protected string $name = '') {}

    /**
     * Returns the configured field name.
     */
    public function getName(): string
    {
        return $this->name;
    }
}
