<?php

namespace Swiftly\Routing\Tests\Collection;

use Swiftly\Routing\Collection\RouteCollection;
use Swiftly\Routing\Route;
use PHPUnit\Framework\TestCase;
use Iterator;
use Countable;

use function array_shift;
use function count;

/**
 * @group Unit
 */
Class RouteCollectionTest Extends TestCase
{

    /** @var RouteCollection $collection */
    private $collection;

    protected function setUp() : void
    {
        $this->collection = new RouteCollection([
            'home' => $this->createRoute( 'home', '/', ['GET'] ),
            'post' => $this->createRoute( 'post', '/post/[i:id]', ['POST'] ),
            'form' => $this->createRoute( 'form', '/form/[s:name]',
                ['GET', 'POST']
            )
        ]);
    }

    private function createRoute(
        string $name,
        string $regex,
        array $methods
    ) : Route {
        $route = new Route();
        $route->name = $name;
        $route->regex = $regex;
        $route->methods = $methods;

        return $route;
    }

    public function testCanGetRoute() : void
    {
        $route = $this->collection->get( 'home' );

        self::assertInstanceOf( Route::class, $route );
        self::assertSame( 'home', $route->name );
        self::assertSame( '/', $route->regex );
        self::assertSame( ['GET'], $route->methods );
    }

    public function testCanAddRoute() : void
    {
        $this->collection->add(
            'test',
            $this->createRoute( 'test', '/my-url', ['GET'] )
        );

        $route = $this->collection->get( 'test' );

        self::assertInstanceOf( Route::class, $route );
        self::assertSame( 'test', $route->name );
        self::assertSame( '/my-url', $route->regex );
        self::assertSame( ['GET'], $route->methods );
    }

    public function testCanRemoveRoute() : void
    {
        $this->collection->remove( 'home' );

        $route = $this->collection->get( 'home' );

        self::assertNull( $route );
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

    public function testCanCountNumberOfRoutes() : void
    {
        self::assertInstanceOf( Countable::class, $this->collection );
        self::assertSame( 3, $this->collection->count() );
        self::assertSame( 3, count( $this->collection ) );
    }

    public function testCompilesRegexForMethod() : void
    {
        $expected = [
            'GET' => '~^(?|(?>/(*:home))|(?>/form/[s:name](*:form)))$~ixX',
            'POST' => '~^(?|(?>/post/[i:id](*:post))|(?>/form/[s:name](*:form)))$~ixX'
        ];

        foreach ( $expected as $http_method => $regex ) {
            $compiled = $this->collection->compile( $http_method );

            self::assertSame( $regex, $compiled );
        }
    }
}
