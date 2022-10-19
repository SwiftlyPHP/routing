<?php

namespace Swiftly\Routing\Tests;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Route;

Class RouteTest Extends TestCase
{
    /** @var Route $route */
    private $route;

    public function setUp(): void
    {
        $this->route = new Route();
    }

    public function testCanGetComponents(): void
    {
        $components = $this->route->getComponents();

        self::assertIsArray($components);
        self::assertIsNotEmpty($components);
        self::assertContainsOnly('string', $components);
    }

    public function testCanCheckIfRouteIsStatic(): void
    {
        self::assertTrue($this->route->isStatic());
    }

    public function testCanGetMethods(): void
    {
        $methods = $this->route->getMethods();

        self::assertIsArray($methods);
        self::assertIsNotEmpty($methods);
        self::assertContainsOnly('string', $methods);
        self::assertContains('GET', $methods);
        self::assertNotContains('POST', $methods);
    }

    public function testCanCheckIfMethodSupported(): void
    {
        self::assertTrue($this->route->supports('GET'));
        self::assertFalse($this->route->supports('POST'));
    }

    public function testCanGetTags(): void
    {
        $tags = $this->route->getTags();

        self::assertIsArray($tags);
        self::assertIsNotEmpty($tags);
        self::assertContainsOnly('string', $tags);
        self::assertContains('admin', $tags);
        self::assertNotContains('cacheable', $tags);
    }

    public function testCanCheckIfRouteHasTag(): void
    {
        self::assertTrue($this->route->hasTag('admin'));
        self::assertFalse($this->route->hasTag('cacheable'));
    }
}
