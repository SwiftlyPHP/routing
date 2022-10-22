<?php

namespace Swiftly\Routing\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Swiftly\Routing\UrlGenerator;
use Swiftly\Routing\Collection;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\UndefinedRouteException;
use Swiftly\Routing\Exception\MissingArgumentException;
use Swiftly\Routing\Exception\InvalidArgumentException;
use Swiftly\Routing\Exception\FormatException;

Class UrlGeneratorTest Extends TestCase
{
    /** @var Collection&MockObject $collection */
    private $collection;

    /** @var UrlGenerator $generator */
    private $generator;

    public function setUp(): void
    {
        $this->collection = self::createMock(Collection::class);
        $this->generator = new UrlGenerator($this->collection);
    }

    /**
     * Creates a mock Route object that returns the given components
     *
     * @psalm-param array<string|ComponentInterface> $components
     *
     * @param string[]|ComponentInterface[] $components Route components
     * @return Route&MockObject                         Mocked route
     */
    private static function createMockRoute(array $components): Route
    {
        $route = self::createMock(Route::class);
        $route->method('getComponents')
            ->willReturn($components);

        return $route;
    }

    /**
     * Creates a mock ComponentInterface with the given name
     *
     * @param string $name                   Component name
     * @return ComponentInterface&MockObject Mocked component
     */
    private static function createMockComponent(string $name): ComponentInterface
    {
        $component = self::createMock(ComponentInterface::class);
        $component->method('name')
            ->willReturn($name);

        return $component;
    }

    public function testCanGenerateUrlForStaticRoute(): void
    {
        $route = self::createMockRoute(['/admin']);

        $this->collection->method('get')
            ->with('admin')
            ->willReturn($route);

        $url = $this->generator->generate('admin');

        self::assertSame('/admin', $url);
    }

    public function testCanGenerateUrlForDynamicRoute(): void
    {
        $component = self::createMockComponent('id');
        $component->method('format')
            ->willReturnArgument(0);

        $route = self::createMockRoute(['/admin/', $component]);

        $this->collection->method('get')
            ->with('edit')
            ->willReturn($route);

        $url = $this->generator->generate('edit', ['id' => 42]);

        self::assertSame('/admin/42', $url);
    }

    public function testThrowsOnUndefinedRoute(): void
    {
        $this->collection->method('get')
            ->with('update')
            ->willReturn(null);

        self::expectException(UndefinedRouteException::class);

        // Named route 'update' does not exist
        $this->generator->generate('update');
    }

    public function testThrowsOnMissingRouteArgument(): void
    {
        $component = self::createMockComponent('id');
        $route = self::createMockRoute(['/admin/', $component]);

        $this->collection->method('get')
            ->with('edit')
            ->willReturn($component);

        self::expectException(MissingArgumentException::class);

        // Route requires 'id' argument - it is missing
        $this->generator->generate('edit');
    }

    public function testThrowsOnInvalidRouteArgument(): void
    {
        $component = self::createMockComponent('id');
        $component->method('format')
            ->willThrowException(self::createStub(FormatException::class));

        $route = self::createMockRoute(['/admin/', $component]);

        $this->collection->method('get')
            ->with('edit')
            ->willReturn($route);

        self::expectException(InvalidArgumentException::class);

        // Route requires 'id' argument to be numeric - it is a string
        $this->generator->generate('edit', ['id' => 'fortytwo']);
    }
}
