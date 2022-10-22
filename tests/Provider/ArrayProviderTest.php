<?php

namespace Swiftly\Routing\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Provider\ArrayProvider;
use Swiftly\Routing\Route;

Class ArrayProviderTest Extends TestCase
{
    /** @var ArrayProvider $provider */
    private $provider;

    public function setUp(): void
    {
        // TODO: Update here when Route constructor finalised
        $this->provider = new ArrayProvider([
            'view'   => new Route(),
            'edit'   => new Route(),
            'delete' => new Route()
        ]);
    }

    /**
     * Asserts the named route exists in the given array.
     *
     * @param string $route    Route name
     * @param Route[] $subject Subject array
     */
    private static function assertArrayContainsRoute(string $route, array $subject): void
    {
        self::assertContainsOnlyInstancesOf(Route::class, $subject);
        self::assertArrayHasKey($route, $subject);
    }

    public function testCanAddSingleRoute(): void
    {
        // TODO: Update here when Route constructor finalised
        $this->provider->add('delete', new Route());

        $routes = $this->provider->provide();

        self::assertArrayContainsRoute('delete', $routes);
    }

    public function testCanLoadRoutesFromArray(): void
    {
        $routes = $this->provider->provide();

        self::assertIsArray($routes);
        self::assertCount(3, $routes);
        self::assertContainsOnlyInstancesOf(Route::class, $routes);

        // Route names MUST be maintained
        self::assertArrayHasKey('view', $routes);
        self::assertArrayHasKey('edit', $routes);
        self::assertArrayHasKey('delete', $routes);
    }
}
