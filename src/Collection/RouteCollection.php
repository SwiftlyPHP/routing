<?php

namespace Swiftly\Routing\Collection;

use Swiftly\Routing\Collection\GenericCollection;
use Swiftly\Routing\Collection\FilterableTrait;
use Swiftly\Routing\Route;

use function in_array;
use function implode;

/**
 * Class used to store and manage a collection of routes
 *
 * @extends GenericCollection<string,Route>
 * @author clvarley
 */
Class RouteCollection extends GenericCollection
{

    /**
     * @use FilterableTrait<Route>
     */
    use FilterableTrait;

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
     * @param string $name Route name
     * @return Route|null  Route object
     */
    public function get( string $name ) : ?Route
    {
        return $this->items[$name] ?? null;
    }

    /**
     * Compiles the regex for the given HTTP method
     *
     * @param string $method HTTP method
     * @return string        Compiled regex
     */
    public function compile( string $method ) : string
    {
        $regexes = [];

        foreach ( $this->items as $name => $route ) {
            if ( $route->supports( $method ) ) {
                $regexes[] = '(?>' . $route->compile() . '(*:' . $name . '))';
            }
        }

        return '~^(?|' . implode( '|', $regexes ) . ')$~ixX';
    }
}
