<?php

namespace Swiftly\Routing\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swiftly\Routing\Matcher\StaticMatcher;
use Swiftly\Routing\Collection;
use Swiftly\Routing\Route;
use Swiftly\Routing\MatchedRoute;

/**
 * @covers \Swiftly\Routing\Matcher\StaticMatcher
 * @uses \Swiftly\Routing\MatchedRoute
 */
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

    public function createMockRoute(): Route
    {
        $route = $this->createMock(Route::class);
        $route->expects(self::exactly(2))
            ->method('getComponent')
            ->with(self::equalTo(0))
            ->willReturn('/admin');

        return $route;
    }

    public function testCanMatchStaticRoute(): void
    {
        $route = $this->createMockRoute();

        $this->collection->method('static')
            ->willReturn(['view' => $route]);

        $match = $this->matcher->match('/admin');

        /**
         * Matchers now return a dedicated @see {MatchedRoute} P.O.D
         */
        self::assertInstanceOf(MatchedRoute::class, $match);
        self::assertSame('view', $match->name);
        self::assertSame($route, $match->route);
        self::assertEmpty($match->args);

        /**
         * Validate matcher returns null on non-existant route
         */
        self::assertNull($this->matcher->match('/settings'));
    }
}
