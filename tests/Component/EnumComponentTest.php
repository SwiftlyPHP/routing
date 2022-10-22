<?php

namespace Swiftly\Routing\Tests\Component;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Component\EnumComponent;
use Swiftly\Routing\Exception\FormatException;

Class EnumComponentTest Extends TestCase
{
    /** @var EnumComponent $component */
    private $component;

    public function setUp(): void
    {
        $this->component = new EnumComponent('action', ['view', 'edit', 'delete']);
    }

    public function testCanGetName(): void
    {
        $name = $this->component->name();

        self::assertSame('action', $name);
    }

    public function testCanGetRegex(): void
    {
        $regex = $this->component->regex();

        self::assertSame('(view|edit|delete)', $regex);
    }

    public function testCanGetAllowedValues(): void
    {
        $values = $this->components->values();

        self::assertIsArray($values);
        self::assertCount(3, $values);
        self::assertContainsOnly('string', $values);

        // Return order is NOT important
        self::assertContains('view', $values);
        self::assertContains('edit', $values);
        self::assertContains('delete', $values);
    }

    public function testCanCheckIfValueIsAccepted(): void
    {
        self::assertTrue($this->component->accepts('view'));
        self::assertTrue($this->component->accepts('edit'));
        self::assertTrue($this->component->accepts('delete'));
        self::assertFalse($this->component->accepts('look'));
        self::assertFalse($this->component->accepts('update'));
        self::assertFalse($this->component->accepts('remove'));
    }

    public function testCanFormatValue(): void
    {
        $formatted = $this->component->format('view');

        self::assertSame('view', $formatted);
    }

    public function testThrowsOnInvalidValue(): void
    {
        self::expectException(FormatException::class);

        $this->component->format('remove');
    }
}
