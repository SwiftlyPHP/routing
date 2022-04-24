<?php

namespace Swiftly\Routing\Tests;

use Swiftly\Routing\Route;
use Swiftly\Routing\ParameterInterface;
use PHPUnit\Framework\TestCase;

use function count;
use function implode;

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
        $route = new Route( $url, function () {} );

        self::assertSame( $regex, $route->compile() );
        self::assertSame( $args, $route->args );
    }

    /** @dataProvider exampleUrlProvider */
    public function testCanGetComponents( string $url, string $regex, array $args ) : void
    {
        $route = new Route( $url, function () {} );

        $components = $route->components();

        self::assertSame( $regex, implode( "", $components ) );

        foreach ( $components as $component ) {
            if ( $component instanceof ParameterInterface ) {
                self::assertContains( $component->name(), $args );
            } else {
                self::assertStringContainsString( $component, $url );
            }
        }
    }
}
