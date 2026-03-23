<?php

declare(strict_types=1);

namespace orange\request\attributes;

use Attribute;
use orange\request\RequestAttribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class Table extends RequestAttribute
{
    public function __construct(protected string $name = '', private string $database = '') {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getDatabase(): string
    {
        return $this->database;
    }
}
