<?php

namespace Swiftly\Routing\Collection;

use Swiftly\Routing\Collection\GenericCollection;
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
            if ( in_array( $method, $route->methods ) ) {
                $regexes[] = '(?>' . $route->compile() . '(*:' . $name . '))';
            }
        }

        return '~^(?|' . implode( '|', $regexes ) . ')$~ixX';
    }
}
