<?php

namespace Swiftly\Routing\Tests\Matcher;

use Swiftly\Routing\Matcher\StaticMatcher;
use Swiftly\Routing\Route;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class StaticMatcherTest Extends TestCase
{

    /**
     * @var StaticMatcher $matcher
     */
    private $matcher;

    public function setUp() : void
    {
        $this->matcher = new StaticMatcher([
            '/home'  => new Route( '/home', function () {} ),
            '/about' => new Route( '/about', function () {} ),
            '/team'  => new Route( '/team', function () {} )
        ]);
    }

    public function testCanMatchRoute() : void
    {
        foreach ( ['/home', '/about', '/team'] as $url ) {
            $route = $this->matcher->match( $url );

            self::assertInstanceOf( Route::class, $route );
            self::assertSame( $url, $route->url );
        }

        // Route not registered, returns null
        self::assertNull( $this->matcher->match( '/contact' ) );
    }
}
