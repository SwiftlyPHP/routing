<?php

namespace Swiftly\Routing\Tests\Component;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Component\StringComponent;
use Swiftly\Routing\Exception\FormatException;

Class StringComponentTest Extends TestCase
{
    /** @var StringComponent $component */
    private $component;

    public function setUp(): void
    {
        $this->component = new StringComponent('slug');
    }

    public function testCanGetName(): void
    {
        $name = $this->component->name();

        self::assertSame('slug', $name);
    }

    public function testCanGetRegex(): void
    {
        $regex = $this->component->regex();

        self::assertSame('([A-Za-z0-9\-\_\@\.]+)', $regex);
    }

    public function testCanCheckIfValueIsValid(): void
    {
        self::assertTrue($this->component->valid('news'));
        self::assertTrue($this->component->valid('example-url'));
        self::assertTrue($this->component->valid('a-url_with@symbols.42'));
        self::assertFalse($this->component->valid('a/url/with/parts'));
        self::assertFalse($this->component->valid('a-url-with#fragment'));
        self::assertFalse($this->component->valid('a-url-with?query=string'));
        self::assertFalse($this->component->valid('')); // Empty string
    }

    public function testCanFormatValue(): void
    {
        $formatted = $this->component->format('a-url');

        self::assertSame('a-url', $formatted);
    }

    public function testThrowsOnInvalidValue(): void
    {
        self::expectException(FormatException::class);

        $this->component->format('a-url-with?query=string');
    }
}
