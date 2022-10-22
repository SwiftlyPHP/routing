<?php

namespace Swiftly\Routing\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Collection;
use Swiftly\Routing\Route;

Class CollectionTest Extends TestCase
{
    /** @var Collection $collection */
    private $collection;

    public function setUp(): void
    {
        // TODO: Update here when Route constructor finalised
        $this->collection = new Collection([
            'view'   => new Route(),
            'edit'   => new Route(),
            'delete' => new Route()
        ]);
    }

    public function testCanCheckIfRouteExists(): void
    {
        self::assertTrue($this->collection->has('view'));
        self::assertTrue($this->collection->has('edit'));
        self::assertTrue($this->collection->has('delete'));
        self::assertFalse($this->collection->has('look'));
        self::assertFalse($this->collection->has('update'));
        self::assertFalse($this->collection->has('remove'));
    }

    public function testCanGetNamedRoute(): void
    {
        self::assertInstanceOf(Route::class, $this->collection->get('view'));
        self::assertInstanceOf(Route::class, $this->collection->get('edit'));
        self::assertInstanceOf(Route::class, $this->collection->get('delete'));
        self::assertNull($this->collection->get('look'));
        self::assertNull($this->collection->get('update'));
        self::assertNull($this->collection->get('remove'));
    }

    public function testCanGetStaticRoutes(): void
    {
        $static = $this->collection->static();

        self::assertIsArray($static);
        self::AsserCount(1, $static);
        self::assertContainsOnlyInstancesOf(Route::class, $static);

        // Route names MUST be maintained
        self::assertArrayHasKey('view', $static);
    }

    public function testCanGetDynamicRoutes(): void
    {
        $dynamic = $this->collection->dynamic();

        self::assertIsArray($dynamic);
        self::AssertCount(2, $dynamic);
        self::assertContainsOnlyInstancesOf(Route::class, $dynamic);

        // Route names MUST be maintained
        self::assertArrayHasKey('edit', $dynamic);
        self::assertArrayHasKey('delete', $dynamic);
    }

    public function testCanGetAllRoutes(): void
    {
        $routes = $this->collection->all();

        self::assertIsArray($routes);
        self::assertCount(3, $routes);
        self::assertContainsOnlyInstancesOf(Route::class, $routes);

        // Route names MUST be maintained
        self::assertArrayHasKey('view', $routes);
        self::assertArrayHasKey('edit', $routes);
        self::assertArrayHasKey('delete', $routes);
    }
}
