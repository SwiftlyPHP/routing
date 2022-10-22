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

        $match = $this->matcher->match('/admin');

        /**
         * Matchers now return a dedicated @see {Match} P.O.D
         */
        self::assertInstanceOf(Match::class, $match);
        self::assertSame('view', $match->name);
        self::assertSame($route, $match->route);
        self::assertEmpty($match->args);
    }
}
