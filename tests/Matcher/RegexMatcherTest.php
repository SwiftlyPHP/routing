<?php

namespace Swiftly\Routing\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swiftly\Routing\Matcher\RegexMatcher;
use Swiftly\Routing\Collection;
use Swiftly\Routing\Route;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Match;

Class RegexMatcherTest Extends TestCase
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

    private function createMockRoute(): Route
    {
        $component = self::createMock(ComponentInterface::class);
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
        $route = self::createMockRoute(Route::class);

        $this->collection->method('dynamic')
            ->willReturn(['view' => $route]);

        $match = $this->matcher->match('/admin/users');

        /**
         * Matchers now return a dedicated @see {Match} P.O.D
         */
        self::assertInstanceOf(Match::class, $match);
        self::assertSame('view', $match->name);
        self::assertSame($route, $match->route);
        self::assertArrayHasKey('page', $match->args);
        self::assertSame('users', $match->args['page']);
    }
}
