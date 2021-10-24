<?php

namespace Swiftly\Routing\Collection;

use Swiftly\Routing\Collection\GenericCollecion;
use Swiftly\Routing\Route;

/**
 * Class used to store and manage a collection of routes
 *
 * @extends GenericCollecion<string,Route>
 * @author clvarley
 */
Class RouteCollection Extends GenericCollecion
{

    /**
     * Adds an existing route to the collection
     *
     * @param string $name Route identifier
     * @param Route $route Route definition
     * @return void        N/a
     */
    public function set( string $name, Route $route ) : void
    {
        $this->items[$name] = $route;
    }

    /**
     * Gets the named route from the collection
     *
     * @param string $name Route identifier
     * @return Route|null  Route definition
     */
    public function get( string $name ) : ?Route
    {
        return $this->items[$name] ?? null;
    }

    /**
     * Compiles the regex for the given HTTP method
     *
     * @param string $method (Optional) HTTP method
     * @return string        Compiled regex
     */
    public function compile( string $method = 'GET' ) : string
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
