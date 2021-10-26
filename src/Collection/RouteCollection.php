<?php

namespace Swiftly\Routing\Collection;

use Swiftly\Routing\Collection\GenericCollection;
use Swiftly\Routing\Route;

/**
 * Class used to store and manage a collection of routes
 *
 * @extends GenericCollection<string,Route>
 */
Class RouteCollection extends GenericCollection
{

    /**
     * Adds a new route to this collection
     *
     * @param string $name      Route name
     * @param string $url       Route URL
     * @param callable $handler Route handler
     * @param string[] $methods Accepted HTTP methods
     */
    public function add( string $name, string $url, $handler, array $methods = [] ) : Route
    {
        $route = new Route( $url, $handler );
        $route->methods = $methods;

        $this->items[$name] = $route;

        return $route;
    }

    /**
     * Adds an existing route to the collection
     *
     * @param string $name Route name
     * @param Route $route Route object
     * @return void        N/a
     */
    public function set( string $name, Route $route ) : void
    {
        $this->items[$name] = $route;
    }

    /**
     * Gets the named route from the collection
     *
     * @psalm-mutation-free
     *
     * @param string $name Route name
     * @return Route|null  Route object
     */
    public function get( string $name ) : ?Route
    {
        return $this->items[$name] ?? null;
    }

    /**
     * Filter collection to routes that support the given HTTP method
     *
     * @psalm-mutation-free
     *
     * @param string $method HTTP method
     * @return self          Filtered content
     */
    public function filterByMethod( string $method ) : self
    {
        $items = [];

        foreach ( $this->items as $name => $item ) {
            if ( $item->supports( $method ) ) {
                $items[$name] = $item;
            }
        }

        return new self( $items );
    }
}
