<?php

declare(strict_types=1);

use orange\request\Request;
use orange\request\attributes\validations\Alpha;
use orange\request\attributes\validations\AlphaDash;
use orange\request\attributes\validations\AlphaNumeric;
use orange\request\attributes\validations\AlphaNumericSpaces;
use orange\request\attributes\validations\Decimal;
use orange\request\attributes\validations\Differs;
use orange\request\attributes\validations\ExactLength;
use orange\request\attributes\validations\GreaterThan;
use orange\request\attributes\validations\GreaterThanEqualTo;
use orange\request\attributes\validations\InList;
use orange\request\attributes\validations\Integer as IntegerValidation;
use orange\request\attributes\validations\IsNatural;
use orange\request\attributes\validations\IsNaturalNoZero;
use orange\request\attributes\validations\IsRequired;
use orange\request\attributes\validations\IsUnique;
use orange\request\attributes\validations\LessThan;
use orange\request\attributes\validations\LessThanEqualTo;
use orange\request\attributes\validations\Matches;
use orange\request\attributes\validations\MaxLength;
use orange\request\attributes\validations\MinLength;
use orange\request\attributes\validations\Numeric;
use orange\request\attributes\validations\RegexMatch;
use orange\request\attributes\validations\ValidBase64;
use orange\request\attributes\validations\ValidEmail;
use orange\request\attributes\validations\ValidEmails;
use orange\request\attributes\validations\ValidIp;
use orange\request\attributes\validations\ValidUrl;

final class ValidationAttributesTest extends UnitTestHelper
{
    protected function makeRequest(array $input): Request
    {
        return new class($input) extends Request {
        };
    }

    public function testAlpha(): void
    {
        $rule = new Alpha();

        $this->assertTrue($rule->validate('Orange'));
        $this->assertFalse($rule->validate('Orange123'));
        $this->assertEquals('This field may only contain alphabetical characters', $rule->getMessage());
        $this->assertEquals('Name may only contain alphabetical characters', $rule->getMessage('Name'));
    }

    public function testAlphaDash(): void
    {
        $rule = new AlphaDash();

        $this->assertTrue($rule->validate('Orange-Test'));
        $this->assertFalse($rule->validate('Orange_Test'));
        $this->assertFalse($rule->validate('Orange_123-Test'));
        $this->assertFalse($rule->validate('Orange Test'));
        $this->assertEquals('This field may only contain alpha-numeric characters, underscores, and dashes', $rule->getMessage());
        $this->assertEquals('Slug may only contain alpha-numeric characters, underscores, and dashes', $rule->getMessage('Slug'));
    }

    public function testAlphaNumeric(): void
    {
        $rule = new AlphaNumeric();

        $this->assertTrue($rule->validate('Orange123'));
        $this->assertFalse($rule->validate('Orange-123'));
        $this->assertEquals('This field may only contain alpha-numeric characters', $rule->getMessage());
        $this->assertEquals('Code may only contain alpha-numeric characters', $rule->getMessage('Code'));
    }

    public function testAlphaNumericSpaces(): void
    {
        $rule = new AlphaNumericSpaces();

        $this->assertTrue($rule->validate('Orange 123'));
        $this->assertFalse($rule->validate('Orange-123'));
        $this->assertEquals('This field may only contain alpha-numeric characters and spaces', $rule->getMessage());
        $this->assertEquals('Title may only contain alpha-numeric characters and spaces', $rule->getMessage('Title'));
    }

    public function testDecimal(): void
    {
        $rule = new Decimal();

        $this->assertTrue($rule->validate('10.25'));
        $this->assertFalse($rule->validate('10'));
        $this->assertEquals('This field must contain a decimal number', $rule->getMessage());
        $this->assertEquals('Price must contain a decimal number', $rule->getMessage('Price'));
    }

    public function testDiffers(): void
    {
        $rule = new Differs('password');
        $rule->request($this->makeRequest(['password' => 'secret']));

        $this->assertTrue($rule->validate('different'));
        $this->assertFalse($rule->validate('secret'));
        $this->assertEquals('This field must differ from password', $rule->getMessage());
        $this->assertEquals('Confirm Password must differ from password', $rule->getMessage('Confirm Password'));
        $this->assertEquals('password', $rule->getField());
    }

    public function testExactLength(): void
    {
        $rule = new ExactLength(5);

        $this->assertTrue($rule->validate('Apple'));
        $this->assertFalse($rule->validate('Pear'));
        $this->assertEquals(5, $rule->getLength());
        $this->assertEquals('This field must be exactly 5 characters', $rule->getMessage());
        $this->assertEquals('Pin must be exactly 5 characters', $rule->getMessage('Pin'));
    }

    public function testGreaterThan(): void
    {
        $rule = new GreaterThan(10);

        ob_start();
        $valid = $rule->validate(11);
        ob_end_clean();

        ob_start();
        $invalid = $rule->validate(10);
        ob_end_clean();

        $this->assertTrue($valid);
        $this->assertFalse($invalid);
        $this->assertEquals(10, $rule->getValue());
        $this->assertEquals('This field must be greater than 10', $rule->getMessage());
        $this->assertEquals('Count must be greater than 10', $rule->getMessage('Count'));
    }

    public function testGreaterThanEqualTo(): void
    {
        $rule = new GreaterThanEqualTo(10);

        $this->assertTrue($rule->validate(10));
        $this->assertTrue($rule->validate('11'));
        $this->assertFalse($rule->validate(9));
        $this->assertEquals(10, $rule->getValue());
        $this->assertEquals('This field must be greater than or equal to 10', $rule->getMessage());
        $this->assertEquals('Count must be greater than or equal to 10', $rule->getMessage('Count'));
    }

    public function testInList(): void
    {
        $rule = new InList(['draft', 'published']);

        $this->assertTrue($rule->validate('draft'));
        $this->assertFalse($rule->validate('archived'));
        $this->assertEquals(['draft', 'published'], $rule->getValues());
        $this->assertEquals('This field must be one of the allowed values', $rule->getMessage());
        $this->assertEquals('Status must be one of the allowed values', $rule->getMessage('Status'));
    }

    public function testInteger(): void
    {
        $rule = new IntegerValidation();

        $this->assertTrue($rule->validate('10'));
        $this->assertTrue($rule->validate(-5));
        $this->assertFalse($rule->validate('10.5'));
        $this->assertEquals('This field must contain an integer', $rule->getMessage());
        $this->assertEquals('Age must contain an integer', $rule->getMessage('Age'));
    }

    public function testIsNatural(): void
    {
        $rule = new IsNatural();

        $this->assertTrue($rule->validate('0'));
        $this->assertTrue($rule->validate('25'));
        $this->assertFalse($rule->validate('-1'));
        $this->assertEquals('This field must contain only natural numbers', $rule->getMessage());
        $this->assertEquals('Count must contain only natural numbers', $rule->getMessage('Count'));
    }

    public function testIsNaturalNoZero(): void
    {
        $rule = new IsNaturalNoZero();

        $this->assertTrue($rule->validate('25'));
        $this->assertFalse($rule->validate('0'));
        $this->assertEquals('This field must contain a natural number greater than zero', $rule->getMessage());
        $this->assertEquals('Count must contain a natural number greater than zero', $rule->getMessage('Count'));
    }

    public function testIsRequired(): void
    {
        $rule = new IsRequired();

        $this->assertTrue($rule->validate('filled'));
        $this->assertFalse($rule->validate(''));
        $this->assertEquals('This field is required', $rule->getMessage());
        $this->assertEquals('Name is required', $rule->getMessage('Name'));
    }

    public function testLessThan(): void
    {
        $rule = new LessThan(10);

        $this->assertTrue($rule->validate(9));
        $this->assertFalse($rule->validate(10));
        $this->assertEquals(10, $rule->getValue());
        $this->assertEquals('This field must be less than 10', $rule->getMessage());
        $this->assertEquals('Count must be less than 10', $rule->getMessage('Count'));
    }

    public function testLessThanEqualTo(): void
    {
        $rule = new LessThanEqualTo(10);

        $this->assertTrue($rule->validate(10));
        $this->assertTrue($rule->validate('9'));
        $this->assertFalse($rule->validate(11));
        $this->assertEquals(10, $rule->getValue());
        $this->assertEquals('This field must be less than or equal to 10', $rule->getMessage());
        $this->assertEquals('Count must be less than or equal to 10', $rule->getMessage('Count'));
    }

    public function testMatches(): void
    {
        $rule = new Matches('password');
        $rule->request($this->makeRequest(['password' => 'secret']));

        $this->assertTrue($rule->validate('secret'));
        $this->assertFalse($rule->validate('different'));
        $this->assertEquals('This field must match password', $rule->getMessage());
        $this->assertEquals('Confirm Password must match password', $rule->getMessage('Confirm Password'));
        $this->assertEquals('password', $rule->getField());
    }

    public function testMaxLength(): void
    {
        $rule = new MaxLength(6);

        $this->assertTrue($rule->validate('Apple'));
        $this->assertFalse($rule->validate('Oranges'));
        $this->assertEquals(6, $rule->getLength());
        $this->assertEquals('This field must be less than 6 characters', $rule->getMessage());
        $this->assertEquals('Name must be less than 6 characters', $rule->getMessage('Name'));
    }

    public function testMinLength(): void
    {
        $rule = new MinLength(3);

        $this->assertTrue($rule->validate('Pear'));
        $this->assertFalse($rule->validate('Fig'));
        $this->assertEquals(3, $rule->getLength());
        $this->assertEquals('This field must be greater than 3 characters', $rule->getMessage());
        $this->assertEquals('Name must be greater than 3 characters', $rule->getMessage('Name'));
    }

    public function testNumeric(): void
    {
        $rule = new Numeric();

        $this->assertTrue($rule->validate('10'));
        $this->assertTrue($rule->validate('10.5'));
        $this->assertFalse($rule->validate('ten'));
        $this->assertEquals('This field must contain only numbers', $rule->getMessage());
        $this->assertEquals('Price must contain only numbers', $rule->getMessage('Price'));
    }

    public function testRegexMatch(): void
    {
        $rule = new RegexMatch('/^[A-Z]{3}[0-9]{3}$/');

        $this->assertTrue($rule->validate('ABC123'));
        $this->assertFalse($rule->validate('abc123'));
        $this->assertEquals('/^[A-Z]{3}[0-9]{3}$/', $rule->getPattern());
        $this->assertEquals('This field is not in the correct format', $rule->getMessage());
        $this->assertEquals('Code is not in the correct format', $rule->getMessage('Code'));
    }

    public function testValidBase64(): void
    {
        $rule = new ValidBase64();

        $this->assertTrue($rule->validate(base64_encode('orange')));
        $this->assertFalse($rule->validate('not-base64'));
        $this->assertEquals('This field must contain a valid base64 string', $rule->getMessage());
        $this->assertEquals('Payload must contain a valid base64 string', $rule->getMessage('Payload'));
    }

    public function testValidEmail(): void
    {
        $rule = new ValidEmail();

        $this->assertTrue($rule->validate('test@example.com'));
        $this->assertFalse($rule->validate('not-an-email'));
        $this->assertEquals('This field must contain a valid email address', $rule->getMessage());
        $this->assertEquals('Email must contain a valid email address', $rule->getMessage('Email'));
    }

    public function testValidEmails(): void
    {
        $rule = new ValidEmails();

        $this->assertTrue($rule->validate('one@example.com, two@example.com'));
        $this->assertFalse($rule->validate('one@example.com, invalid'));
        $this->assertEquals('This field must contain only valid email addresses', $rule->getMessage());
        $this->assertEquals('Recipients must contain only valid email addresses', $rule->getMessage('Recipients'));
    }

    public function testValidIp(): void
    {
        $rule = new ValidIp();
        $ipv4Rule = new ValidIp('ipv4');
        $ipv6Rule = new ValidIp('ipv6');

        $this->assertTrue($rule->validate('127.0.0.1'));
        $this->assertTrue($ipv4Rule->validate('127.0.0.1'));
        $this->assertTrue($ipv6Rule->validate('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
        $this->assertFalse($ipv4Rule->validate('2001:0db8:85a3:0000:0000:8a2e:0370:7334'));
        $this->assertEquals('ipv4', $ipv4Rule->getVersion());
        $this->assertEquals('This field must contain a valid IP address', $rule->getMessage());
        $this->assertEquals('Address must contain a valid IP address', $rule->getMessage('Address'));
    }

    public function testValidUrl(): void
    {
        $rule = new ValidUrl();

        $this->assertTrue($rule->validate('https://example.com/path'));
        $this->assertFalse($rule->validate('not-a-url'));
        $this->assertEquals('This field must contain a valid URL', $rule->getMessage());
        $this->assertEquals('Website must contain a valid URL', $rule->getMessage('Website'));
    }
}
