<?php declare(strict_types=1);

namespace Swiftly\Routing\Tests\Matcher;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Collection;
use Swiftly\Routing\MatchedRoute;
use Swiftly\Routing\Matcher\StaticMatcher;
use Swiftly\Routing\Route;

/**
 * @covers \Swiftly\Routing\Matcher\StaticMatcher
 * @uses \Swiftly\Routing\MatchedRoute
 */
class StaticMatcherTest extends TestCase
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

    /**
     * @return MockObject&Route
     */
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
        $route->expects(self::exactly(2))
            ->method('supports')
            ->with('GET')
            ->willReturn(true);

        $this->collection->method('static')
            ->willReturn(['view' => $route]);

        $match = $this->matcher->match('/admin');

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
