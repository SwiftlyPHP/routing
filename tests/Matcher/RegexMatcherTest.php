<?php declare(strict_types=1);

namespace Swiftly\Routing\Tests\Matcher;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Collection;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\MatchedRoute;
use Swiftly\Routing\Matcher\RegexMatcher;
use Swiftly\Routing\Route;

/**
 * @covers \Swiftly\Routing\Matcher\RegexMatcher
 * @uses \Swiftly\Routing\MatchedRoute
 */
class RegexMatcherTest extends TestCase
{
    /** @var Collection&MockObject $collection */
    private $collection;

    /** @var RegexMatcher $matcher */
    private $matcher;

    public function setUp(): void
    {
        $this->collection = self::createMock(Collection::class);
        $this->matcher = new RegexMatcher($this->collection);
    }

    /**
     * @return MockObject&Route
     */
    private function createMockRoute(): Route
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('name')
            ->willReturn('page');
        $component->method('regex')
            ->willReturn('(users)');

        $route = self::createMock(Route::class);
        $route->method('getComponents')
            ->willReturn(['/admin/', $component]);

        return $route;
    }

    public function testCanMatchDynamicRoute(): void
    {
        $route = self::createMockRoute();
        $route->expects(self::once())
            ->method('supports')
            ->with('GET')
            ->willReturn(true);

        $this->collection->method('dynamic')
            ->willReturn(['view' => $route]);

        $this->collection->method('get')
            ->with("view")
            ->willReturn($route);

        $match = $this->matcher->match('/admin/users');

        /**
         * Matchers now return a dedicated @see {MatchedRoute} P.O.D
         */
        self::assertInstanceOf(MatchedRoute::class, $match);
        self::assertSame('view', $match->name);
        self::assertSame($route, $match->route);
        self::assertArrayHasKey('page', $match->args);
        self::assertSame('users', $match->args['page']);

        /**
         * Validate matcher returns null on non-existant route
         */
        self::assertNull($this->matcher->match('/admin/settings'));
    }
}
