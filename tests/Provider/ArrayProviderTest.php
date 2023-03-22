<?php

namespace Swiftly\Routing\Tests\Provider;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Provider\ArrayProvider;
use Swiftly\Routing\Route;

/**
 * @covers \Swiftly\Routing\Provider\ArrayProvider
 */
Class ArrayProviderTest Extends TestCase
{
    /** @var ArrayProvider $provider */
    private $provider;

    public function setUp(): void
    {
        $this->provider = new ArrayProvider([
            'view'   => self::createStub(Route::class),
            'edit'   => self::createStub(Route::class),
            'delete' => self::createStub(Route::class)
        ]);
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

    public function testCanAddSingleRoute(): void
    {
        $this->provider->add('update', self::createStub(Route::class));

        $routes = $this->provider->provide();

        self::assertIsArray($routes);
        self::assertCount(4, $routes);
        self::assertArrayHasKey('update', $routes);
    }
}
