<?php

namespace Swiftly\Routing\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swiftly\Routing\Matcher\StaticMatcher;
use Swiftly\Routing\Collection;
use Swiftly\Routing\Route;

Class StaticMatcherTest Extends TestCase
{
    /** @var Collection&MockObject $collection */
    private $collection;

    /** @var StaticMatcher $matcher */
    private $matcher;

    public function setUp(): void
    {
        $this->collection = self::createMock(Collection::class);
        $this->matcher = new StaticMatcher($this->collection);
    }

    public function testCanMatchStaticRoute(): void
    {
        // TODO: Make route return static component at index [0]
        $route = self::createMock(Route::class);

        $this->collection->method('static')
            ->willReturn(['view' => $route]);

        $matched = $this->matcher->match('/admin');

        /*
         * Expected return format is: [0 => 'name', 1 => Route, 2 => string[]]
         *
         * 0 => The actual route name, in this case 'view'
         * 1 => The matched Route object
         * 2 => Any matched URL args (in this case none)
         */
        self::assertIsArray($matched);
        self::assertCount(3, $matched);

        self::assertArrayHasKey(0, $matched); // Route name
        self::assertSame('view', $matched[0]);
        self::assertArrayHasKey(1, $matched); // Route object
        self::assertSame($route, $matched[1]);
        self::assertArrayHasKey(2, $matched); // Additional args
        self::assertIsArray(2, $matched[2]);
        self::assertIsEmpty(2, $matched[2]);
    }
}
