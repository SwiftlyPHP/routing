<?php declare(strict_types=1);

namespace Swiftly\Routing\Tests;

use OutOfBoundsException;
use PHPUnit\Framework\TestCase;
use Swiftly\Routing\ComponentInterface;
use Swiftly\Routing\Route;

class RouteTest extends TestCase
{
    /** @var Route $route */
    private $route;

    public function setUp(): void
    {
        $this->route = new Route(
            ['/admin'],
            function () { return 'hello'; },
            ['GET'],
            ['admin']
        );
    }

    public function testCanGetComponents(): void
    {
        $components = $this->route->getComponents();

        self::assertIsArray($components);
        self::assertNotEmpty($components);
        self::assertContainsOnly('string', $components);
    }

    public function testCanGetComponentAtIndex(): void
    {
        $component = $this->route->getComponent(0);

        self::assertSame('/admin', $component);
    }

    public function testCanGetHandler(): void
    {
        $handler = $this->route->getHandler();

        self::assertIsCallable($handler);
        self::assertSame('hello', $handler());
    }

    public function testCanGetMethods(): void
    {
        $methods = $this->route->getMethods();

        self::assertIsArray($methods);
        self::assertNotEmpty($methods);
        self::assertContainsOnly('string', $methods);
        self::assertContains('GET', $methods);
        self::assertNotContains('POST', $methods);
    }

    public function testCanGetTags(): void
    {
        $tags = $this->route->getTags();

        self::assertIsArray($tags);
        self::assertNotEmpty($tags);
        self::assertContainsOnly('string', $tags);
        self::assertContains('admin', $tags);
        self::assertNotContains('cacheable', $tags);
    }

    public function testCanCheckIfRouteIsStatic(): void
    {
        self::assertTrue($this->route->isStatic());
    }

    public function testCanCheckIfRouteIsDynamic(): void
    {
        $component = self::createStub(ComponentInterface::class);
        $route = new Route(['/', $component], function () {});

        self::assertFalse($route->isStatic());
    }

    public function testCanCheckIfHttpMethodSupported(): void
    {
        self::assertTrue($this->route->supports('GET'));
        self::assertFalse($this->route->supports('POST'));
    }

    /**
     * @testdox Can check if http method supported (case-insensitive)
     */
    public function testCanCheckIfHttpMethodSupportedCaseInsensitive(): void
    {
        self::assertTrue($this->route->supports('get'));
        self::assertFalse($this->route->supports('post'));
    }

    public function testCanCheckIfRouteHasTag(): void
    {
        self::assertTrue($this->route->hasTag('admin'));
        self::assertFalse($this->route->hasTag('cacheable'));
    }

    /**
     * @testdox Can check if route has tag (case-insensitive)
     */
    public function testCanCheckIfRouteHasTagCaseInsensitive(): void
    {
        self::assertTrue($this->route->hasTag('ADMIN'));
        self::assertFalse($this->route->hasTag('CACHEABLE'));
    }

    public function testThrowsOnInvalidComponentIndex(): void
    {
        self::expectException(OutOfBoundsException::class);

        $this->route->getComponent(1); // Index doesn't exist
    }
}
