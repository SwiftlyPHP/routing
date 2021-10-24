<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Route;
use Iterator;

/**
 * Common interface for managing a collection of routes
 *
 * @author clvarley
 */
Interface CollectionInterface Extends Iterator
{

    /**
     * Adds a new route to the collection
     *
     * @param string $url          Route URL
     * @param callable $controller Route handler
     * @param string[] $methods    Acceptable HTTP methods
     * @return Route               New route
     */
    public function add( string $url, callable $controller, array $methods = [] ) : Route;

    /**
     * Adds an existing route to the collection
     *
     * @param string $name Route identifier
     * @param Route $route Route definition
     * @return void        N/a
     */
    public function set( string $name, Route $route ) : void;

    /**
     * Gets the named route from the collection
     *
     * @param string $name Route identifier
     * @return Route|null  Route definition
     */
    public function get( string $name ) : ?Route;

    /**
     * Compiles the regex for the given HTTP method
     *
     * @param string $method (Optional) HTTP method
     * @return string        Compiled regex
     */
    public function compile( string $method = 'GET' ) : string;

}
