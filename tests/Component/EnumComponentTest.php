<?php declare(strict_types=1);

namespace Swiftly\Routing\Tests\Component;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Component\EnumComponent;
use Swiftly\Routing\Exception\FormatException;

class EnumComponentTest extends TestCase
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

    public function testCanCheckIfValueIsAccepted(): void
    {
        self::assertTrue($this->component->accepts('view'));
        self::assertTrue($this->component->accepts('edit'));
        self::assertTrue($this->component->accepts('delete'));
        self::assertFalse($this->component->accepts('look'));
        self::assertFalse($this->component->accepts('update'));
        self::assertFalse($this->component->accepts('remove'));
        self::assertFalse($this->component->accepts([]));
        self::assertFalse($this->component->accepts(null));
    }

    public function testCanEscapeValue(): void
    {
        $formatted = $this->component->escape('view');

        self::assertSame('view', $formatted);
    }

    public function testThrowsOnInvalidValue(): void
    {
        self::expectException(FormatException::class);

        $this->component->escape('remove');
    }
}
