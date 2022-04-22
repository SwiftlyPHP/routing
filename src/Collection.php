<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Route;

/**
 * Represents a collection of named routes
 */
Final Class Collection
{

    /**
     * Underlying collection of routes
     *
     * @psalm-var array<string, Route> $routes
     *
     * @var Route[] $routes
     */
    private $routes;

    /**
     * Creates a new route collection
     *
     * @psalm-param array<string, Route> $routes
     *
     * @param Route[] $routes
     */
    public function __construct( array $routes = [] )
    {
        $this->routes = $routes;
    }

    /**
     * Adds a new route to the collection
     *
     * @param string $name Route name
     * @param Route $route Route definition
     */
    public function set( string $name, Route $route ) : void
    {
        $this->routes[$name] = $route;
    }

    /**
     * Retrieves a named route from the collection
     *
     * @psalm-immutable
     *
     * @param string $name Route name
     * @return Route|null  Route definition
     */
    public function get( string $name ) : ?Route
    {
        return $this->routes[$name] ?? null;
    }

    /**
     * Check to see if this collection contains any routes
     *
     * @psalm-immutable
     *
     * @return bool Is empty?
     */
    public function isEmpty() : bool
    {
        return empty( $this->routes );
    }

    /**
     * Return a new collection of routes that pass the provided filter
     *
     * @psalm-immutable
     * @psalm-param callable(Route):bool $filter
     *
     * @param callable $filter Filter function
     */
    public function filter( callable $filter ) : Collection
    {
        $routes = [];

        foreach ( $this->routes as $name => $route ) {
            if ( $filter( $route ) ) {
                $routes[$name] = $route;
            }
        }

        return new self( $routes );
    }
}
