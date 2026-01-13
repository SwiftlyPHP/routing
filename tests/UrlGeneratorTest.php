<?php declare(strict_types=1);

namespace Swiftly\Routing\Tests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Collection;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Exception\FormatException;
use Swiftly\Routing\Exception\InvalidArgumentException;
use Swiftly\Routing\Exception\MissingArgumentException;
use Swiftly\Routing\Exception\UndefinedRouteException;
use Swiftly\Routing\Route;
use Swiftly\Routing\UrlGenerator;

class UrlGeneratorTest extends TestCase
{
    /** @var Collection&MockObject $collection */
    private $collection;

    /** @var UrlGenerator $generator */
    private $generator;

    public function setUp(): void
    {
        $this->collection = $this->createMock(Collection::class);
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
    private function createMockRoute(array $components): Route
    {
        $route = $this->createMock(Route::class);
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
    private function createMockComponent(string $name): ComponentInterface
    {
        $component = $this->createMock(ComponentInterface::class);
        $component->method('name')
            ->willReturn($name);

        return $component;
    }

    public function testCanGenerateUrlForStaticRoute(): void
    {
        $route = $this->createMockRoute(['/admin']);

        $this->collection->method('get')
            ->with('admin')
            ->willReturn($route);

        $url = $this->generator->generate('admin');

        self::assertSame('/admin', $url);
    }

    public function testCanGenerateUrlForDynamicRoute(): void
    {
        $component = $this->createMockComponent('id');
        $component->method('escape')
            ->willReturnArgument(0);

        $route = $this->createMockRoute(['/admin/', $component]);

        $this->collection->method('get')
            ->with('edit')
            ->willReturn($route);

        $url = $this->generator->generate('edit', ['id' => '42']);

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
        $component = $this->createMockComponent('id');
        $route = $this->createMockRoute(['/admin/', $component]);

        $this->collection->method('get')
            ->with('edit')
            ->willReturn($route);

        self::expectException(MissingArgumentException::class);

        // Route requires 'id' argument - it is missing
        $this->generator->generate('edit');
    }

    public function testThrowsOnInvalidRouteArgument(): void
    {
        $component = $this->createMockComponent('id');
        $component->method('escape')
            ->willThrowException(self::createStub(FormatException::class));

        $route = $this->createMockRoute(['/admin/', $component]);

        $this->collection->method('get')
            ->with('edit')
            ->willReturn($route);

        self::expectException(InvalidArgumentException::class);

        // Route requires 'id' argument to be numeric - it is a string
        $this->generator->generate('edit', ['id' => 'fortytwo']);
    }
}
