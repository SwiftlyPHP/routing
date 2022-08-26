<?php

namespace Swiftly\Routing\Tests;

use Swiftly\Routing\UrlGenerator;
use Swiftly\Routing\Route;
use Swiftly\Routing\Collection;
use Swiftly\Routing\Exception\RouteNotFoundException;
use Swiftly\Routing\Exception\MissingArgumentException;
use Swiftly\Routing\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/**
 * @group Unit
 */
Class UrlGeneratorTest Extends TestCase
{

    /**
     * @var UrlGenerator $generator
     */
    private $generator;

    public function setUp() : void
    {
        $routes = new Collection([
            'home'    => new Route( '/', function () {} ),
            'about'   => new Route( '/about', function () {} ),
            'contact' => new Route( '/contact', function () {} ),
            'team'    => new Route( '/team/[s:member]', function () {} ),
            'post'    => new Route( '/post/[i:year]/[s:slug]', function () {} )
        ]);

        $this->generator = new UrlGenerator($routes);
    }

    public function testCanGenerateStaticRoute() : void
    {
        self::assertSame( '/', $this->generator->generate( 'home' ) );
        self::assertSame( '/about', $this->generator->generate( 'about' ) );
        self::assertSame( '/contact', $this->generator->generate( 'contact' ) );
    }

    public function testCanGenerateDynamicRoute() : void
    {
        self::assertSame( '/team/john', $this->generator->generate( 'team', [
            'member' => 'john'
        ]));
        self::assertSame( '/post/2022/example-post', $this->generator->generate( 'post', [
            'year' => 2022,
            'slug' => 'example-post'
        ]));
    }

    public function testThrowsOnMissingRoute() : void
    {
        $this->expectException( RouteNotFoundException::class );

        $this->generator->generate( 'missing' );
    }

    public function testThrowsOnMissingParameter() : void
    {
        $this->expectException( MissingArgumentException::class );

        $this->generator->generate( 'team', [] );
    }

    public function testThrowsOnInvalidParameter() : void
    {
        $this->expectException( InvalidArgumentException::class );

        $this->generator->generate( 'post', ['year' => 'twothousand'] );
    }
}
