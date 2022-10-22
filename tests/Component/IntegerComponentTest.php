<?php

namespace Swiftly\Routing\Tests\Component;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Component\IntegerComponent;
use Swiftly\Routing\Exception\FormatException;

Class IntegerComponentTest Extends TestCase
{
    /** @var IntegerComponent $component */
    private $component;

    public function setUp(): void
    {
        $this->component = new IntegerComponent('id');
    }

    public function testCanGetName(): void
    {
        $name = $this->component->name();

        self::assertSame('id', $name);
    }

    public function testCanGetRegex(): void
    {
        $regex = $this->component->regex();

        self::assertSame('(\d+)', $regex);
    }

    public function testCanCheckIfValueIsAccepted(): void
    {
        self::assertTrue($this->component->accepts(42));
        self::assertTrue($this->component->accepts('42'));
        self::assertFalse($this->component->accepts(4.2));
        self::assertFalse($this->component->accepts('4.2'));
        self::assertFalse($this->component->accepts('fortytwo'));
    }

    public function testCanFormatValue(): void
    {
        $formatted = $this->component->format(42);

        self::assertSame('42', $formatted);
    }

    public function testThrowsOnInvalidValue(): void
    {
        self::expectException(FormatException::class);

        $this->component->format(4.2);
    }
}
