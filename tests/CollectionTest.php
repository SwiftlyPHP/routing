<?php

namespace Swiftly\Routing\Tests;

use Swiftly\Routing\Collection;
use Swiftly\Routing\Route;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class CollectionTest Extends TestCase
{

    /** @var Collection $collection */
    private $collection;

    public function setUp() : void
    {
        $this->collection = new Collection([
            'home'    => new Route( '/', function () {} ),
            'about'   => new Route( '/about', function () {} ),
            'login'   => new Route( '/login', function () {} ),
            'dynamic' => new Route( '/[i:id]', function () {} ),
        ]);
    }

    public function testCanGetRoutes() : void
    {
        $route = $this->collection->get( 'home' );

        self::assertInstanceOf( Route::class, $route );
        self::assertSame( '/', $route->url );
        self::assertNull( $this->collection->get( 'nothing' ) );
    }

    public function testCanSetRoutes() : void
    {
        $route = new Route( '/posts', function () {} );

        $this->collection->set( 'posts', $route );

        self::assertInstanceOf( Route::class, $this->collection->get( 'posts' ) );
        self::assertSame( $route, $this->collection->get( 'posts' ) );
    }

    public function testCanFilterRoutes() : void
    {
        $collection = $this->collection->filter( function ( $route ) {
            return !$route->isStatic();
        });

        self::assertNotSame( $collection, $this->collection );
        self::assertInstanceOf( Route::class, $collection->get( 'dynamic' ) );
        self::assertNull( $collection->get( 'home' ) );
        self::assertNull( $collection->get( 'about' ) );
        self::assertNull( $collection->get( 'login' ) );
    }

    public function testCanGetAllRoutes() : void
    {
        $routes = $this->collection->all();

        self::assertCount( 4, $routes );
        self::assertArrayHasKey( 'home', $routes );
        self::assertArrayHasKey( 'about', $routes );
        self::assertArrayHasKey( 'login', $routes );
        self::assertArrayHasKey( 'dynamic', $routes );
        self::assertInstanceOf( Route::class, $routes['home'] );
        self::assertInstanceOf( Route::class, $routes['about'] );
        self::assertInstanceOf( Route::class, $routes['login'] );
        self::assertInstanceOf( Route::class, $routes['dynamic'] );
    }

    public function testCanTellIfEmpty() : void
    {
        self::assertFalse( $this->collection->isEmpty() );

        $empty_collection = new Collection([]);

        self::assertTrue( $empty_collection->isEmpty() );
    }
}
