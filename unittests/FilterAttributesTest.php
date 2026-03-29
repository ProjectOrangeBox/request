<?php

declare(strict_types=1);

use orange\request\attributes\filters\StrLimit;
use orange\request\attributes\filters\ToBoolean;
use orange\request\attributes\filters\ToInteger;
use orange\request\attributes\filters\ToString;

final class FilterAttributesTest extends UnitTestHelper
{
    public function testToString(): void
    {
        $rule = new ToString();

        $this->assertSame('123', $rule->filter(123));
        $this->assertSame('1', $rule->filter(true));
        $this->assertSame('', $rule->filter(null));
    }

    public function testToInteger(): void
    {
        $rule = new ToInteger();

        $this->assertSame(123, $rule->filter('123'));
        $this->assertSame(10, $rule->filter('10 apples'));
        $this->assertSame(0, $rule->filter(false));
    }

    public function testToBoolean(): void
    {
        $rule = new ToBoolean();

        $this->assertTrue($rule->filter(1));
        $this->assertFalse($rule->filter(0));
        $this->assertTrue($rule->filter('true'));
        $this->assertFalse($rule->filter('false'));
        $this->assertTrue($rule->filter('yes'));
        $this->assertFalse($rule->filter(''));
    }

    public function testStrLimit(): void
    {
        $rule = new StrLimit(5);

        $this->assertSame('Hello', $rule->filter('Hello World'));
        $this->assertSame('Hey', $rule->filter('Hey'));
        $this->assertSame(12345, $rule->filter(12345));
    }
}
