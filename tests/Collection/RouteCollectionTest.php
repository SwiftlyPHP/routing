<?php

namespace Swiftly\Routing\Tests\Collection;

use PHPUnit\Framework\TestCase;
use Swiftly\Routing\Collection\RouteCollection;
use Swiftly\Routing\Route;
use Iterator;

use function array_shift;
use function count;

/**
 * @group Unit
 */
Class RouteCollectionTest Extends TestCase
{

    const COMPILED_REGEX = [
        'GET' => '~^(?|(?>/(*:home))|(?>/form/([a-zA-Z0-9-_]+)(*:form)))$~ixX',
        'POST' => '~^(?|(?>/post/(\d+)(*:post))|(?>/form/([a-zA-Z0-9-_]+)(*:form)))$~ixX'
    ];

    /** @var RouteCollection $collection */
    private $collection;

    protected function setUp() : void
    {
        $this->collection = new RouteCollection([
            'home' => $this->createRoute( '/', ['GET'] ),
            'post' => $this->createRoute( '/post/[i:id]', ['POST'] ),
            'form' => $this->createRoute( '/form/[s:name]', ['GET', 'POST'] )
        ]);
    }

    private function createRoute(
        string $url,
        array $methods
    ) : Route {
        $route = new Route( $url, function () {} );
        $route->methods = $methods;

        return $route;
    }

    public function testCanGetRoute() : void
    {
        $route = $this->collection->get( 'home' );

        self::assertInstanceOf( Route::class, $route );
        self::assertSame( '/', $route->raw );
        self::assertSame( ['GET'], $route->methods );
    }

    public function testCanSetRoute() : void
    {
        $this->collection->set(
            'test',
            $this->createRoute( '/my-url', ['GET'] )
        );

        $route = $this->collection->get( 'test' );

        self::assertInstanceOf( Route::class, $route );
        self::assertSame( '/my-url', $route->raw );
        self::assertSame( ['GET'], $route->methods );
    }

    public function testCanIterateOverCollection() : void
    {
        $routes = ['home', 'post', 'form'];

        self::assertInstanceOf( Iterator::class, $this->collection );

        foreach ( $this->collection as $name => $route ) {
            $current = array_shift( $routes );

            self::assertSame( $current, $name );
            self::assertInstanceOf( Route::class, $route );
        }

        self::assertEmpty( $routes );
    }

    public function testCompilesRegexForMethod() : void
    {
        foreach ( self::COMPILED_REGEX as $http_method => $expected ) {
            $regex = $this->collection->compile( $http_method );

            self::assertSame( $expected, $regex );
        }
    }
}
