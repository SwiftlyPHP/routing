<?php

namespace Swiftly\Routing\Tests\Provider;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swiftly\Routing\Provider\FileProvider;
use Swiftly\Routing\FileLoaderInterface;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Route;

/**
 * @covers \Swiftly\Routing\FileProvider
 * @uses \Swiftly\Routing\Route
 */
Class FileProviderTest Extends TestCase
{
    /** @var FileLoaderInterface&MockObject $loader */
    private $loader;

    /** @var FileProvider $provider */
    private $provider;

    private const EXAMPLE_ROUTES = [
        'view' => [
            'handler' => 'PostController::view', 
            'path'    => '/posts',
            'methods' => ['GET', 'HEAD'],
            'tags'    => ['cacheable']
        ],
        'edit' => [
            'handler' => 'PostController::edit',
            'path'    => '/edit/[i:post_id]',
            'tags'    => ['admin', 'no-cache']
        ],
        'delete' => [
            'handler' => 'some_functional_controller',
            'path'    => '/delete/[i:user_id]/posts',
            'methods' => ['GET', 'POST']
        ]
    ];

    private static $EXAMPLE_COMPONENTS;

    public function setUp(): void
    {
        $this->loader = self::createMock(FileLoaderInterface::class);
        $this->provider = new FileProvider($this->loader);
    }

    public function setUpBeforeClass(): void
    {
        self::$EXAMPLE_COMPONENTS = [
            'view'   => ['/posts'],
            'edit'   => ['/edit/', self::createMock(ComponentInterface::class)],
            'delete' => ['/delete/', self::createMock(ComponentInterface::class), '/posts']
        ];
    }

    private static function assertRouteMatchesExample(
        Route $route,
        string $name
    ): void {
        self::assertRouteHasCorrectHandler($route, $name);
        self::assertRouteHasCorrectComponents($route, $name);
        self::assertRouteHasCorrectMethods($route, $name);
        self::assertRouteHasCorrectTags($route, $name);
    }

    private static function assertRouteHasCorrectHandler(
        Route $route,
        string $name
    ): void {
        $expected_handler = self::EXAMPLE_ROUTES[$name]['handler'];

        self::assertSame(
            $expected_handler,
            $route->getHandler(),
            "Route '$name' does not have the correct handler!"
        );
    }

    private static function assertRouteHasCorrectComponents(
        Route $route,
        string $name
    ): void {
        self::assertSame(
            self::$EXAMPLE_COMPONENTS[$name],
            $route->getComponents(), 
            "Route '$name' does not have the correct components!"
        );
    }

    private static function assertRouteHasCorrectMethods(
        Route $route,
        string $name
    ): void {
        $expected_methods = self::EXAMPLE_ROUTES[$name]['methods'] ?? ['GET'];

        self::assertSame(
            $expected_methods,
            $route->getMethods(),
            "Route '$name' does not support the correct HTTP methods!"
        );
    }

    private static function assertRouteHasCorrectTags(
        Route $route,
        string $name
    ): void {
        $expected_tags = self::EXAMPLE_ROUTES[$name]['tags'] ?? [];

        self::assertSame(
            $expected_tags,
            $route->getTags(),
            "Route '$name' does not have the correct tags!"
        );
    }

    public function testCanLoadRoutesFromFile(): void
    {
        $loader = &$this->loader;
        $loader->expects(self::once())
            ->method('load')
            ->willReturn(self::EXAMPLE_ROUTES);

        $routes = $this->provider->provide();

        self::assertIsArray($routes);
        self::assertCount(3, $routes);
        self::assertContainsOnlyInstancesOf(Route::class, $routes);

        // Route names MUST be maintained
        self::assertArrayHasKey('view', $routes);
        self::assertArrayHasKey('edit', $routes);
        self::assertArrayHasKey('delete', $routes);

        // Routes must match file definition
        self::assertRouteMatchesExample($routes['view'], 'view');
        self::assertRouteMatchesExample($routes['edit'], 'edit');
        self::assertRouteMatchesExample($routes['delete'], 'delete');
    }
}
