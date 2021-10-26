<?php

namespace Swiftly\Routing;

use Swiftly\Routing\Route;

/**
 * Interface for all classes capable of matching URLs to routes
 */
Interface MatcherInterface
{

    /**
     * Attempt to find a route that satisfies the given URL
     *
     * @param string $url Requested URL
     * @return Route|null Matching route
     */
    public function match( string $url ) : ?Route;

}
