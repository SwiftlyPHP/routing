<?php

namespace Swiftly\Routing\Tests;

use Swiftly\Routing\Route;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class RouteTest Extends TestCase
{

    public function exampleUrlProvider() : array
    {
        return [
            [
                '/example/[s:name]',          // Route url
                '/example/([a-zA-Z0-9-_]+)',  // Expected regex
                [ 'name' ]                    // Expected args
            ],
            [
                '/news/[i:year]/[i:month]',
                '/news/(\d+)/(\d+)',
                [ 'year', 'month' ]
            ],
            [
                '/account/[i:id]/[s:page]',
                '/account/(\d+)/([a-zA-Z0-9-_]+)',
                [ 'id', 'page' ]
            ],
            [
                '/static',
                '/static',
                []
            ],
            [
                '/[s:slug]/gallery',
                '/([a-zA-Z0-9-_]+)/gallery',
                [ 'slug' ]
            ]
        ];
    }

    /** @dataProvider exampleUrlProvider */
    public function testCanCompileRoutes( string $url, string $regex, array $args ) : void
    {
        $route = new Route;
        $route->raw = $url;

        self::assertSame( $regex, $route->compile() );
        self::assertSame( $args, $route->args );
    }
}
