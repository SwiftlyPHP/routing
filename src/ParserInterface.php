<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Collection\RouteCollection;

/**
 * Interface for classes that parse route files
 *
 * @author clvarley
 */
Interface ParserInterface
{

    /**
     * Parse the given routes file and return an array of routes
     *
     * @param string $filename Path to file
     * @return RouteCollection Route collection
     */
    public function parse( string $filename ) : RouteCollection;

}
