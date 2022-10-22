<?php

namespace Swiftly\Routing\Tests\Matcher;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swiftly\Routing\Matcher\RegexMatcher;
use Swiftly\Routing\Collection;
use Swiftly\Routing\Route;
use Swiftly\Routing\ComponentInterface;

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

        $matched = $this->matcher->match('/admin/users');

        /*
         * Expected return format is: [0 => 'name', 1 => Route, 2 => string[]]
         *
         * 0 => The actual route name, in this case 'view'
         * 1 => The matched Route object
         * 2 => Any matched URL args (in this case 'users')
         */
        self::assertIsArray($matched);
        self::assertCount(3, $matched);

        self::assertArrayHasKey(0, $matched); // Route name
        self::assertSame('view', $matched[0]);
        self::assertArrayHasKey(1, $matched); // Route object
        self::assertSame($route, $matched[1]);
        self::assertArrayHasKey(2, $matched); // Additional args
        self::assertIsArray(2, $matched[2]);
        self::assertArrayHasKey('page', $matched[2]);
        self::assertSame('users', $matched[2]['page']);
    }
}
