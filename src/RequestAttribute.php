<?php

declare(strict_types=1);

namespace orange\request;

class RequestAttribute
{
    protected array $request;

    public function request(array $request): void
    {
        $this->request = $request;
    }
}
