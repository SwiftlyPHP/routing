<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Collection;
use Swiftly\Routing\Exception\RouteNotFoundException;
use Swiftly\Routing\Exception\MissingArgumentException;

/**
 * Generates URLs for routes based on provided args
 *
 * @psalm-immutable
 */
Class UrlGenerator
{

    /**
     * @var Collection $routes
     */
    private $routes;

    /**
     * Create a new URL generator for the given routes
     *
     * @param Collection $routes Route collection
     */
    public function __construct( Collection $routes )
    {
        $this->routes = $routes;
    }

    /**
     * Generates a new URL for the named route using the provided args
     *
     * @psalm-param array<string, scalar> $args
     *
     * @throws RouteNotFoundException   If the named route does not exist
     * @throws MissingArgumentException If a required argument is missing
     *
     * @param string $name Route name
     * @param array $args  Route arguments
     */
    public function generate( string $name, array $args = [] ) : string
    {
        $route = $this->routes->get( $name );

        if ( !$route ) {
            throw new RouteNotFoundException( $name );
        }



        // TODO



        return '';
    }
}
