<?php

namespace Swiftly\Routing\Tests\Provider;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swiftly\Routing\Provider\FileProvider;
use Swiftly\Routing\FileLoaderInterface;
use Swiftly\Routing\ParserInterface;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Route;
use Swiftly\Routing\Exception\RouteParseException;

use function strpos;
use function explode;

/**
 * @covers \Swiftly\Routing\Provider\FileProvider
 * @covers \Swiftly\Routing\Exception\RouteParseException
 * @uses \Swiftly\Routing\Route
 */
Class FileProviderTest Extends TestCase
{
    /** @var FileLoaderInterface&MockObject $loader */
    private $loader;

    /** @var ParserInterface&MockObject $parser */
    private $parser;

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
            // Functional controllers are allowed too!
            'handler' => 'phpinfo',
            'path'    => '/delete/[i:user_id]/posts',
            'methods' => ['GET', 'POST']
        ]
    ];

    private static $EXAMPLE_COMPONENTS;

    public function setUp(): void
    {
        $this->loader = $this->createMock(FileLoaderInterface::class);
        $this->parser = $this->createMock(ParserInterface::class);
        $this->provider = new FileProvider($this->loader, $this->parser);

        self::$EXAMPLE_COMPONENTS = [
            'view'   => ['/posts'],
            'edit'   => ['/edit/', $this->createMock(ComponentInterface::class)],
            'delete' => ['/delete/', $this->createMock(ComponentInterface::class), '/posts']
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

        // Provider should convert to callable array syntax for us
        if (strpos($expected_handler, '::', 1) !== false) {
            $expected_handler = explode('::', $expected_handler, 2);
        }

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

        $parser = &$this->parser;
        $parser->expects(self::exactly(3))
            ->method('parse')
            ->withConsecutive(
                [self::EXAMPLE_ROUTES['view']['path']],
                [self::EXAMPLE_ROUTES['edit']['path']],
                [self::EXAMPLE_ROUTES['delete']['path']]
            )
            ->willReturn(
                self::$EXAMPLE_COMPONENTS['view'],
                self::$EXAMPLE_COMPONENTS['edit'],
                self::$EXAMPLE_COMPONENTS['delete']
            );

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

    public function testThrowsOnMissingRouteName(): void
    {
        $loader = &$this->loader;
        $loader->expects(self::once())
            ->method('load')
            ->willReturn([
                [
                    'handler' => 'PostController::view',
                    'path'    => '/posts'
                ]
            ]);

        $this->expectException(RouteParseException::class);

        $this->provider->provide();
    }

    public function testThrowsOnMissingRouteDefinition(): void
    {
        $loader = &$this->loader;
        $loader->expects(self::once())
            ->method('load')
            ->willReturn([
                'view' => null
            ]);

        $this->expectException(RouteParseException::class);

        $this->provider->provide();
    }

    public function testThrowsOnMissingRoutePath(): void
    {
        $loader = &$this->loader;
        $loader->expects(self::once())
            ->method('load')
            ->willReturn([
                'view' => [
                    'handler' => 'PostController::view'
                ]
            ]);

        $this->expectException(RouteParseException::class);

        $this->provider->provide();
    }

    public function testThrowsOnMissingRouteHandler(): void
    {
        $loader = &$this->loader;
        $loader->expects(self::once())
            ->method('load')
            ->willReturn([
                'view' => [
                    'path' => '/posts'
                ]
            ]);

        $this->expectException(RouteParseException::class);

        $this->provider->provide();
    }

    public function testThrowsWhenHandlerIsNotAFunction(): void
    {
        $loader = &$this->loader;
        $loader->expects(self::once())
            ->method('load')
            ->willReturn([
                'view' => [
                    'path'    => '/posts',
                    'handler' => 'some_unknown_func'
                ]
            ]);

        $this->expectException(RouteParseException::class);

        $this->provider->provide();
    }
}
