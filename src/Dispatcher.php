<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Collection\RouteCollection;
use Swiftly\Routing\CompilerInterface;
use Swiftly\Routing\Route;

use function rtrim;
use function in_array;

/**
 * Simple regex dispatcher
 */
Class Dispatcher
{

    /**
     * Collection of routes
     *
     * @var RouteCollection $routes Route collection
     */
    private $routes;

    /**
     * Compiler used to create the route matcher
     *
     * @var CompilerInterface $compiler Match compiler
     */
    private $compiler;

    /**
     * HTTP methods supported by this router
     *
     * @var string[] ALLOWED_METHODS HTTP methods
     */
    const ALLOWED_METHODS = [
        'OPTIONS',
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE'
    ];

    /**
     * Create a new dispatcher for the given routes
     *
     * @param RouteCollection $routes     Route collection
     * @param CompilerInterface $compiler Match compiler
     */
    public function __construct( RouteCollection $routes, CompilerInterface $compiler )
    {
        $this->routes = $routes;
        $this->compiler = $compiler;
    }

    /**
     * Returns all the routes that match the URL
     *
     * @param string $method HTTP method
     * @param string $url    URL path
     * @return Route|null    Route definition
     */
    public function dispatch( string $method, string $url ) : ?Route
    {
        $url = rtrim( $url, " \n\r\t\0\x0B\\/" );

        if ( empty( $url ) ) {
            $url = '/';
        }

        if ( !in_array( $method, self::ALLOWED_METHODS ) ) {
            $method = 'GET';
        }

        // Filter to routes that support the method
        $routes = $this->routes->filter(
            function ( Route $route ) use ( $method ) : bool {
                return $route->supports( $method );
            }
        );

        // Try and match the route!
        $route = $this->compiler->compile( $routes )->match( $url );

        return $route;
    }
}
