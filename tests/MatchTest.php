<?php

namespace Swiftly\Routing\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Match;
use Swiftly\Routing\Route;

Class MatchTest Extends TestCase
{
    /** @var Match $match */
    private $match;

    public function setUp(): void
    {
        $this->match = new Match('view', self::createStub(Route::class), []);
    }

    public function testCanAccessProperties(): void
    {
        /**
         * WARNING: This is an internal class and should not be relied upon
         *
         * These tests are here to help catch any breaking changes, but do
         * guarantee that the property names will not change in future.
         *
         * If you have any references to the @see {Match} class in your code
         * I suggest you remove them - it is not part of the public API.
         */
        self::assertSame('view', $this->match->name);
        self::assertInstanceOf(Route::class, $this->match->route);
        self::assertSame([], $this->match->args);
    }
}
